<?php
    
namespace Inanimatt\SiteBuilder\Serialiser;

class FileSerialiser implements SerialiserInterface
{
    protected $outputPath;
    protected $outputExtension;

    public function __construct($outputPath, $outputExtension)
    {
        $this->outputPath = $outputPath;
        $this->outputExtension = $outputExtension;
    }

    public function write($content, $name)
    {
        $path = $this->outputPath.DIRECTORY_SEPARATOR.$name;
        
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }
        
        file_put_contents($path, $content);
    }

    public function getOutputExtension()
    {
        return $this->outputExtension;
    }
}