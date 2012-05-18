<?php
namespace Inanimatt\SiteBuilder\Exception;

class ArgumentException extends SiteBuilderException 
{


    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return sprintf('Invalid argument: %s', parent::getMessage());
    }
    
}
