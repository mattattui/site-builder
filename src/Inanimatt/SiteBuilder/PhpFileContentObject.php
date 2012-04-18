<?php

namespace Inanimatt\SiteBuilder;
use Symfony\Component\Finder\SplFileInfo;

class PhpFileContentObject extends SplFileInfo implements ContentObjectInterface
{
    protected $view = null;

    public function __construct($file, $relativePath, $relativePathName)
    {
        parent::__construct($file, $relativePath, $relativePathName);
        
        $view = new SiteBuilderTemplate();
        ob_start();
        require($this->getRealPath());
        $view->content = ob_get_clean();
        
        $this->view = $view;
    }

    public function getName()
    {
        return $this->getRelativePathName();
    }

    public function getType()
    {
        return 'PhpFile';
    }
    
    public function getContent()
    {
        return $this->view->content;
    }
    
    public function getMetadata()
    {
        return $this->view->__getVars();
    }
    
}