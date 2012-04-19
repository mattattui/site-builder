<?php

namespace Inanimatt\SiteBuilder\ContentHandler;

interface ContentHandlerInterface
{

    public function getName();
    
    public function getContent();
    
    public function getMetadata();
    
}