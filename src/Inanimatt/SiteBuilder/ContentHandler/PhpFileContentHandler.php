<?php

namespace Inanimatt\SiteBuilder\ContentHandler;

use Inanimatt\SiteBuilder\SiteBuilderTemplate;
use Inanimatt\SiteBuilder\Exception\SiteBuilderException;
use Symfony\Component\Finder\SplFileInfo;

class PhpFileContentHandler extends SplFileInfo implements ContentHandlerInterface
{
    protected $view = null;

    public function __construct($file, $relativePath, $relativePathName)
    {
        if (!is_file($file)) {
            throw new SiteBuilderException('File not found.');
        }

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

    public function getContent()
    {
        return $this->view->content;
    }

    public function getMetadata()
    {
        return $this->view->__getVars();
    }

    public function getOutputName()
    {
        // Strip current file extension, replace with outputExtension

        $ext_pos = strrpos($this->getName(), '.');
        if ($ext_pos === false) {
            throw new SiteBuilderException('Unexpected filename; must have file extension');
        }
        $filename = substr($this->getName(), 0, $ext_pos) . '.html';

        return $filename;
    }

}
