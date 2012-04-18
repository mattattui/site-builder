<?php
namespace Inanimatt\SiteBuilder;

class SiteBuilderConfig implements \ArrayAccess
{
  
    protected $config = array(
        'template_path'    => null,
        'default_template' => 'template.php',
        'output_dir'       => 'output',
        'content_dir'      => 'content',
        'output_extension' => '.html',
    );
 
 
    public static function load($configFile)
    {
        return new self(parse_ini_file($configFile));
    }
 
    public function __construct(array $config)
    {
        $this->config['template_path'] = __DIR__.'/../../../';
        
        foreach($config as $key => $value) {
            $this[$key] = $value;
        }
        
    }


    public function offsetSet($offset, $value) {
        if (!isset($offset)) {
            throw new SiteBuilderConfigException('Invalid option: '.$offset);
        } else {
            $this->check($offset, $value);
            $this->config[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->config[$offset]);
    }
    
    public function offsetUnset($offset) {
        // Don't actually unset, just null. Otherwise setting will fail.
        $this->config[$offset] = null;
    }
    
    public function offsetGet($offset) {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }
    
    
    protected function check($option, $value)
    {
        if ($option == 'default_template' && !is_file($this->config['template_path'].$value)) {
            throw new SiteBuilderException('Default template not found! Check your configuration.');
        }

        if ($option == 'output_dir' && !is_dir($value)) {
            throw new SiteBuilderException('Output directory not found! Check your configuration.');
        }

        if ($option == 'content_dir' && !is_dir($value)) {
            throw new SiteBuilderException('Content directory not found! Check your configuration.');
        }

        if ($option == 'output_dir' && !is_writeable($value)) {
            throw new SiteBuilderException('Output directory not writeable. Check your directory permissions.');
        }
        
        return true;
    }
    
}