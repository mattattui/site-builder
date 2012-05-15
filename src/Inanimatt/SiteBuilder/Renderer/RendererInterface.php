<?php
namespace Inanimatt\SiteBuilder\Renderer;

interface RendererInterface
{

    /**
     * Render data through template
     * 
     * @param array $data Array (or array-like object) containing at least a 'content' key
     * @param string $template Template name that makes sense to the renderer (usually a file)
     * @return string Rendered HTML output
     */
    public function render($data, $template);

}