<?php

namespace Inanimatt\SiteBuilder\ContentHandler;

interface ContentHandlerInterface
{

    public function getName();
    
    public function getType();
    
    public function getContent();
    
    public function getMetadata();
    
}