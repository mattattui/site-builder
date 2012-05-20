<?php
namespace Inanimatt\SiteBuilder\Exception;

/**
 * Argument Exception.
 * 
 * Argument exceptions are thrown when invalid data is passed to a method.
 * For example missing or invalid files, unexpected type, etc.
 */
class ArgumentException extends SiteBuilderException 
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Argument exception: %s', $message);
        return parent::__construct($message, $code, $previous);
    }
    
}
