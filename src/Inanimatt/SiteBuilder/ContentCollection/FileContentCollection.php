<?php
namespace Inanimatt\SiteBuilder\ContentCollection;
use Symfony\Component\Finder\Finder;
use Inanimatt\SiteBuilder\ContentHandler\PhpFileContentHandler;
use Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler;

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
            // FIXME: Abstract this to a factory with registered handlers
            if ($file->getExtension() == 'php') {
                $files[] = new PhpFileContentHandler($file, $file->getRelativePath(), $file->getRelativePathName());
            }
            
            if ($file->getExtension() == 'md') {
                $files[] = new MarkdownFileContentHandler($file, $file->getRelativePath(), $file->getRelativePathName());
            }
        }
            
        return $files;

    }

}