<?php
namespace Inanimatt\SiteBuilder;

interface ContentCollectionInterface
{

    public function __construct($path);
  
    public function getObjects();

}