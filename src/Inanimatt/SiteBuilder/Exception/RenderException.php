<?php
namespace Inanimatt\SiteBuilder\Exception;

/**
 * Renderer Exception.
 * 
 * Renderers throw exceptions when they fail to parse templates, encounter
 * invalid data, etc.
 */
class RenderException extends SiteBuilderException 
{
    
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Renderer exception: %s', $message);
        return parent::__construct($message, $code, $previous);
    }
    
}
