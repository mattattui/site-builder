<?php
    
namespace Inanimatt\SiteBuilder\Serialiser;
use Inanimatt\SiteBuilder\Exception\SerialiserException;

class FileSerialiser implements SerialiserInterface
{
    protected $outputPath;

    public function __construct($outputPath)
    {
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0777, true);
        }
        
        if (!is_dir($outputPath)) {
            throw new SerialiserException('Failed to create output directory '.$outputPath);
        }

        $this->outputPath = $outputPath;
    }

    public function write($content, $name)
    {
        $path = $this->outputPath.DIRECTORY_SEPARATOR.$name;
        
        file_put_contents($path, $content);
    }

}