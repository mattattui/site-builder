<?php
namespace Inanimatt\SiteBuilder\Exception;

/**
 * Serialiser Exception.
 *
 * Thrown by serialisers, usually when they are unable to write to their
 * output device.
 */

class SerialiserException extends SiteBuilderException
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Serialiser exception: %s', $message);

        return parent::__construct($message, $code, $previous);
    }

}
