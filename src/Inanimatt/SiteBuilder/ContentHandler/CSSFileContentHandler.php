<?php

namespace Inanimatt\SiteBuilder\ContentHandler;

use Inanimatt\SiteBuilder\SiteBuilderTemplate;
use Inanimatt\SiteBuilder\SiteBuilderException;
use Symfony\Component\Finder\SplFileInfo;

class CSSFileContentHandler extends SplFileInfo implements ContentHandlerInterface
{
    protected $content;
    protected $metadata;
    
    public function __construct($file, $relativePath, $relativePathName)
    {
        if (!is_file($file))
        {
            throw new SiteBuilderException('File not found.');
        }

        parent::__construct($file, $relativePath, $relativePathName);
        
        $this->content = file_get_contents($this->getRealPath());

        $this->metadata = array(
            'template' => 'none',
            'content' => $this->content,
        );
            
    }

    public function getName()
    {
        return $this->getRelativePathName();
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function getMetadata()
    {
        return $this->metadata;
    }
    
    

    public function getOutputName()
    {
        // Strip current file extension, replace with outputExtension
        
        $ext_pos = strrpos($this->getName(), '.');
        if ($ext_pos === false) {
            throw new SiteBuilderException('Unexpected filename; must have file extension');
        }
        $filename = substr($this->getName(), 0, $ext_pos) . '.css';
        
        return $filename;
    }   
    
}