<?php
namespace Inanimatt\SiteBuilder\Renderer;

class TwigRenderer implements RendererInterface
{
    protected $twig;

    /**
     * Create a new renderer
     * 
     * @param Twig_Environment $twig A Twig_Environment instance
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public function render($data, $template)
    {
        return $this->twig->render($template, $data);
    }

}