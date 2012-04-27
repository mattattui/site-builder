<?php
    
namespace Inanimatt\SiteBuilder\Serialiser;

interface SerialiserInterface
{

    public function write($content, $path);
    
    public function getOutputExtension();
}