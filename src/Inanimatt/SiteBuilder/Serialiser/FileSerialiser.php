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
        $name = $this->convertName($name);
        $path = $this->outputPath.DIRECTORY_SEPARATOR.$name;
        
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path));
        }
        
        file_put_contents($path, $content);
    }


    protected function convertName($name)
    {   
        // Strip current file extension, replace with outputExtension
        
        $ext_pos = strrpos($name, '.');
        if ($ext_pos === false) {
            throw new SiteBuilderException('Unexpected filename; must have file extension');
        }
        $filename = substr($name, 0, $ext_pos) . $this->outputExtension;
        
        return $filename;
    }

}