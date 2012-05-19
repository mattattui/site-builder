<?php

namespace Inanimatt\SiteBuilder\ContentHandler;

interface ContentHandlerInterface
{
    
    /**
     * Return the unique name for this content.
     * 
     * @return string A pathlike unique name
     */
    public function getName();
    
    
    /**
     * Return the HTML representation of this content.
     * 
     * @return string HTML representation of content
     */
    public function getContent();
    
    
    /**
     * Fetch content metadata.
     * 
     * Content metadata always includes a key called 'content', and may 
     * also include a 'template' key, and arbitrary other data, e.g. title.
     * 
     * @return array Content metadata
     */
    public function getMetadata();
    
    
    /**
     * Get the relative output path for this content.
     * 
     * This corresponds to an output URL, and is the full filename
     * of the returned file.
     */
    public function getOutputName();
    
}