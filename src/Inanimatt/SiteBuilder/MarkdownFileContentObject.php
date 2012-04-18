<?php

namespace Inanimatt\SiteBuilder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Parser as YamlParser;
use dflydev\markdown\MarkdownParser;

class MarkdownFileContentObject extends SplFileInfo implements ContentObjectInterface
{
    protected $content;
    protected $metadata;
    
    public function __construct($file, $relativePath, $relativePathName)
    {
        parent::__construct($file, $relativePath, $relativePathName);
        
        $yaml = new YamlParser;
        $markdown = new MarkdownParser;
        
        $fileContent = file_get_contents($this->getRealPath());

        // Search for front-matter, parse it, and then remove it
        $data = array();
        $frontMatter = $this->getFrontMatter($fileContent);
        $data = $yaml->parse($frontMatter);
            
        /* Strip (from the start of the string) the length of the front-matter 
         * collected, plus the size of the delimiters, and the starting and 
         * ending line-breaks
         * FIXME: This assumes CRLF (or whatever PHP_EOL turns out to be), which
         * is bad.
         */
        $fileContent = substr($fileContent, mb_strlen($frontMatter, 'UTF-8') + 6 + (2 * strlen(PHP_EOL)));
            
        /* Parse remaining file as markdown */
        $data['content'] = $markdown->transformMarkdown($fileContent);
            
        $data['template'] = isset($data['template']) ? $data['template'] : $this->config['default_template'];
        
        // Create a view so that rendering will still work
        $view = new SiteBuilderTemplate();
        $view->__setVars($data);
        
        $this->metadata = $data;
        $this->content = $data['content'];
        
    }

    public function getName()
    {
        return $this->getRelativePathName();
    }

    public function getType()
    {
        return 'MarkdownFile';
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function getMetadata()
    {
        return $this->metadata;
    }
    
    
    protected function getFrontMatter($content)
    {
        $content = ltrim($content);

        if ((substr($content,0,3) === '---') && preg_match('/^\-\-\-/m', $content, $matches, PREG_OFFSET_CAPTURE, 3)) {
            $end = $matches[0][1];
            return substr($content,3,$end-3);
        }
        
    }
    
    
}