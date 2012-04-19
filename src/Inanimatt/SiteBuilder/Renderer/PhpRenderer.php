<?php
namespace Inanimatt\SiteBuilder\Renderer;

use Inanimatt\SiteBuilder\SiteBuilderTemplate;

class PhpRenderer implements RendererInterface
{
    protected $templatePath;
    
    public function __construct($templatePath)
    {
        $this->templatePath = $templatePath;
    }
    
    public function render($data, $template)
    {
        $view = new SiteBuilderTemplate;
        $view->__setVars($data);
        return $view->render($this->templatePath.$template);
    }

}