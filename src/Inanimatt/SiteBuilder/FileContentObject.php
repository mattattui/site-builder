<?php

namespace Inanimatt\SiteBuilder;
use Symfony\Component\Finder\SplFileInfo;

class FileContentObject extends SplFileInfo implements ContentObjectInterface
{

    public function getName()
    {
        return $this->getRelativePathName();
    }

    public function getType()
    {
        return ucfirst($this->getExtension()).'File';
    }
    
    public function getContent()
    {
        return file_get_contents($this->getRealPath());
    }
    
}