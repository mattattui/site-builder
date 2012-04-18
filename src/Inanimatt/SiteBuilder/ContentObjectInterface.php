<?php

namespace Inanimatt\SiteBuilder;

interface ContentObjectInterface
{

    public function getName();
    
    public function getType();
    
    public function getContent();
    
    public function getMetadata();
    
}