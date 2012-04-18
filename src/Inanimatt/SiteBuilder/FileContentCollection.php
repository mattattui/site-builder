<?php
namespace Inanimatt\SiteBuilder;
use Symfony\Component\Finder\Finder;

class FileContentCollection implements ContentCollectionInterface
{
    protected $path;
    protected $finder;
    protected $collection;

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
            //FIXME: call ContentObjectFactory to get the right filecontentobject for the given extension
            $files[] = new FileContentObject($file, $file->getRelativePath(), $file->getRelativePathName());
        }
            
        return $files;

    }

}