<?php
    
namespace Inanimatt\SiteBuilder\Serialiser;

class FileSerialiser implements SerialiserInterface
{
    protected $outputPath;

    public function __construct($outputPath)
    {
        $this->outputPath = $outputPath;
    }

    public function write($content, $name)
    {
        $path = $this->outputPath.DIRECTORY_SEPARATOR.$name;
        
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }
        
        file_put_contents($path, $content);
    }

}