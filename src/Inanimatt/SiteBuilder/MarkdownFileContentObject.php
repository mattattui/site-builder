<?php

namespace Inanimatt\SiteBuilder;
use Symfony\Component\Finder\SplFileInfo;

class MarkdownFileContentObject extends SplFileInfo implements ContentObjectInterface
{

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
        return file_get_contents($this->getRealPath());
    }
    
    public function getMetadata()
    {
    }
    
}