<?php
namespace Inanimatt\SiteBuilder\ContentCollection;

interface ContentCollectionInterface
{

    public function __construct($path);
  
    public function getObjects();

    public function registerContentHandler($handlerName, $extensions);

}