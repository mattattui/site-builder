<?php
namespace Inanimatt\SiteBuilder\Exception;

class ArgumentException extends SiteBuilderException 
{


    /**
     * @inheritDoc
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Argument exception: %s', $message);
        return parent::__construct($message, $code, $previous);
    }
    
}
