<?php
namespace Inanimatt\SiteBuilder\Renderer;

class TwigRenderer implements RendererInterface
{
    protected $twig;

    /**
     * Create a new renderer
     * 
     * @param Twig_Loader $twig A Twig_Loader instance
     */
    public function __construct(\Twig_Loader $twig)
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