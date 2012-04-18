<?php
namespace Inanimatt\SiteBuilder;
use Symfony\Component\Finder\Finder;

class FileContentCollection implements ContentCollectionInterface
{
    protected $path;
    protected $finder;

    public function __construct($path = null)
    {
        $this->finder = new Finder;
        
        if ($path) {
            $this->setPath($path);
        }
    }
  
    public function setPath($path)
    {
        $this->path = $path;
    }
  
    public function getObjects()
    {
        $files = array();
        
        $this->finder->files()
            ->in($this->path)
            ->name('*.php')
            ->name('*.md')
        ;
            
        foreach($this->finder as $file) {
            $files[] = $file;
        }
            
        return $files;

    }

}