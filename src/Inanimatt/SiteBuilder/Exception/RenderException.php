<?php
namespace Inanimatt\SiteBuilder\Exception;

class RenderException extends SiteBuilderException 
{
    
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Renderer exception: %s', $message);
        return parent::__construct($message, $code, $previous);
    }
    
}
