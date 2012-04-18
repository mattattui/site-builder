<?php
namespace Inanimatt\SiteBuilder;

/* Bulk template rendering class
 * 
 * Usage:
 *   $builder = new SiteBuilder($config_array);
 * or:
 *   $builder = SiteBuilder::load('config.ini');
 * then:
 *   $builder->renderSite();
 * 
 * renderSite() renders every file in the content directory and
 * saves it in the output directory with the output file extension.
 * 
 * You can also call renderFile($filename) to return the rendered
 * content of a single file, then display or save it yourself.
 */


class SiteBuilder
{
    protected $config   = null;
    protected $twig     = null;
    protected $yaml     = null;
    protected $markdown = null;
    
    protected $contentCollection = null;
    protected $serialiser = null;
    
    
    public function __construct($config, $twig, $yaml, $markdown, ContentCollectionInterface $contentCollection, SerialiserInterface $serialiser)
    {
        $this->config   = $config;
        $this->twig     = $twig;
        $this->yaml     = $yaml;
        $this->markdown = $markdown;
        
        $this->contentCollection = $contentCollection;
        $this->serialiser = $serialiser;
    }
    
    
    // Get a list of content objects, run the renderer on each one, then serialise them
    // - content collection, content object
    // - renderer object
    // - serialiser
    public function renderSite()
    {
        $contentObjects = $this->contentCollection->getObjects();
        foreach($contentObjects as $content) {
            $output = $this->renderFile($content);
            
            $this->serialiser->write($output, $content->getName());
        }
    }
    
    protected function getOutputFilename(\SplFileInfo $file)
    {   
        $path = str_replace(realpath($this->config['content_dir'].DIRECTORY_SEPARATOR), realpath($this->config['output_dir']), $file->getRealPath());
        $ext_pos = strrpos($path, '.');
        if ($ext_pos === false) {
            throw new SiteBuilderException('Unexpected filename; must have file extension');
        }
        $filename = substr($path, 0, $ext_pos) . $this->config['output_extension'];
        
        return $filename;
    }
    
    public function renderFile(\SplFileInfo $file)
    {
        // Handle content and data first
        $extension = $file->getExtension();
        
        if ($extension == 'md') {
            // Parse Markdown template
            
            $fileContent = file_get_contents($file->getPathname());

            // Search for front-matter, parse it, and then remove it
            $data = array();
            $frontMatter = $this->getFrontMatter($fileContent);
            $data = $this->yaml->parse($frontMatter);
            
            /* Strip (from the start of the string) the length of the front-matter 
             * collected, plus the size of the delimiters, and the starting and 
             * ending line-breaks
             * FIXME: This assumes CRLF (or whatever PHP_EOL turns out to be), which
             * is bad.
             */
            $fileContent = substr($fileContent, mb_strlen($frontMatter, 'UTF-8') + 6 + (2 * strlen(PHP_EOL)));
            
            
            /* Parse remaining file as markdown */
            $data['content'] = $this->markdown->transformMarkdown($fileContent);
            
            $template = isset($data['template']) ? $data['template'] : $this->config['default_template'];
            
            // Create a view so that rendering will still work
            $view = new SiteBuilderTemplate();
            $view->__setVars($data);
        
        } elseif ($extension == 'php') {
            $view = new SiteBuilderTemplate();
            $view->template = $this->config['default_template'];
        
            // Capture output
            // FIXME: this could really do with being sandboxed or isolated somehow
            ob_start();
            require($file->getPathname());
            $view->content = ob_get_clean();
            
            $data = $view->__getVars();
            $template = $view->template;
            
        } else {
            throw new SiteBuilderException('Unsupported file extension: '.$file->getFilename());
        }
        


        // Insert content into template
        $templateInfo = new \splFileInfo($template);
        if ($templateInfo->getExtension() == 'twig') {
            return $this->twig->render($template, $data);
        }
        
        // Return rendered view
        
        return $view->render($this->config['template_path'].$template);
    }
    
    
    
    public function getFrontMatter($content)
    {
        $content = ltrim($content);

        if ((substr($content,0,3) === '---') && preg_match('/^\-\-\-/m', $content, $matches, PREG_OFFSET_CAPTURE, 3)) {
            $end = $matches[0][1];
            return substr($content,3,$end-3);
        }
        
    }
}

