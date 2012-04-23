<?php
namespace Inanimatt\SiteBuilder\ContentCollection;
use Symfony\Component\Finder\Finder;
use Inanimatt\SiteBuilder\SiteBuilderException;

class FileContentCollection implements ContentCollectionInterface
{
    protected $path;
    protected $finder;
    protected $collection;
    
    protected $handlers;

    public function __construct($path = null)
    {
        $this->finder = new Finder;
        
        if ($path) {
            $this->setPath($path);
        }
        
        $this->handlers = array();
    }
  
    public function registerContentHandler($handlerName, $extensions)
    {
        if (!is_array($extensions)) {
            throw new SiteBuilderException('Invalid argument: 2nd argument must be an array.');
        }
        
        foreach($extensions as $ext) {
            $this->handlers[$ext] = $handlerName;
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
        ;

        foreach($this->handlers as $ext => $className) {
            $this->finder->name('*.'.$ext);
        }
        
        foreach($this->finder as $file) {
            $extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION); // splFileInfo->getExtension requires php 5.3.6
            if (!isset($this->handlers[$extension])) {
                throw new SiteBuilderException('No content handler registered for file extension .'.$extension);
            }
            
            $class = $this->handlers[$extension];
            $files[] = new $class($file, $file->getRelativePath(), $file->getRelativePathName());
        }
            
        return $files;

    }

}