<?php
namespace Inanimatt\SiteBuilder;


if (basename($_SERVER['PHP_SELF']) == basename(__FILE__))
{
    die("This file is a library, it's not intended to be run directly. Try running 'rebuild.php' instead.".PHP_EOL);
}




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
    protected $config = array(
        'template_path'    => null,
        'default_template' => 'template.php',
        'output_dir'       => 'output',
        'content_dir'      => 'content',
        'output_extension' => '.html',
    );
    
    protected $twig     = null;
    protected $yaml     = null;
    protected $markdown = null;
    protected $finder   = null;
    
    public function __construct($config = array())
    {
        $this->config['template_path'] = __DIR__.'/../../../';
        $this->config = array_merge($this->config, $config);
        
        $this->checkConfiguration();
        
        $loader         = new \Twig_Loader_Filesystem(realpath($this->config['template_path']));
        $this->twig     = new \Twig_Environment($loader);
        $this->yaml     = new \Symfony\Component\Yaml\Parser;
        $this->finder   = new \Symfony\Component\Finder\Finder;
        $this->markdown = new \dflydev\markdown\MarkdownParser;
    }
    
    public function checkConfiguration($config = null)
    {
        if (is_null($config)) {
            $config = $this->config;
        }
        
        if (!is_array($config)) {
            throw new SiteBuilderException('Invalid or missing configuration.');
        }

        if (!isset($config['default_template']) || !is_file($config['template_path'].$config['default_template'])) {
            throw new SiteBuilderException('Default template not found! Check your configuration.');
        }

        if (!isset($config['output_dir']) || !is_dir($config['output_dir'])) {
            throw new SiteBuilderException('Output directory not found! Check your configuration.');
        }

        if (!isset($config['content_dir']) || !is_dir($config['content_dir'])) {
            throw new SiteBuilderException('Content directory not found! Check your configuration.');
        }

        if (!isset($config['output_dir']) || !is_writeable($config['output_dir'])) {
            throw new SiteBuilderException('Output directory not writeable. Check your directory permissions.');
        }
    }
    
    
    // Factory method for creating a Site_Builder object from a config file
    public static function load($configFile)
    {
        $config = parse_ini_file($configFile);
        
        return new SiteBuilder($config);
    }
    

    public function renderSite()
    {
        $files = $this->getFiles($this->config['content_dir']);
        foreach($files as $file) {
            $outputFilename = $this->getOutputFilename($file);
            
            $output = $this->renderFile($file);
            
            if (!is_dir(dirname($outputFilename))) {
                mkdir(dirname($outputFilename));
            }
            
            file_put_contents($outputFilename, $output);
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
            if (!$this->twig)
            {
                throw new SiteBuilderException('Twig template given, but Twig not found.');
            }
            
            return $this->twig->render($template, $data);
        }
        
        // Return rendered view
        
        return $view->render($this->config['template_path'].$template);
    }
    
    public function getFiles($directory)
    {
        $files = array();
        
        if ($this->finder) {
            $this->finder->files()
                ->in($directory)
                ->name('*.php');
            
            if ($this->markdown) {
                $this->finder->name('*.md');
            }
            
            foreach($this->finder as $file) {
                $files[] = $file;
            }
            
            return $files;
        }
        
        // Use fallback if Finder not installed. Doesn't descend directories.
        $dir = new \FilesystemIterator($directory);
        foreach($dir as $file) {
            if (!$file->isFile()) {
                continue;
            }
            
            $files[] = $file;
        }
        
        return $files;
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

