<?php

// Utility function - shortcut to htmlspecialchars(). Feel free to comment it out if it's in the way.
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


/* Bulk template rendering class
 * 
 * Usage:
 *   $builder = new Site_Builder($config_array);
 * or:
 *   $builder = Site_Builder::load('config.ini');
 * then:
 *   $builder->renderSite();
 * 
 * renderSite() renders every file in the content directory and
 * saves it in the output directory with the output file extension.
 * 
 * You can also call renderFile($filename) to return the rendered
 * content of a single file, then display or save it yourself.
 */


class Site_Builder
{
	protected $config = array(
		'default_template' => 'template.php',
		'output_dir'       => 'output',
		'content_dir'      => 'content',
		'output_extension' => '.html',
	);
	
	
	public function __construct($config = array())
	{
		$this->config = array_merge($this->config, $config);
		
		// Check configuration
		if (!is_file($this->config['default_template']))
		{
			throw new Site_Builder_Exception('Default template not found! Check your configuration.');
		}

		if (!is_dir($this->config['output_dir']))
		{
			throw new Site_Builder_Exception('Output directory not found! Check your configuration.');
		}

		if (!is_dir($this->config['content_dir']))
		{
			throw new Site_Builder_Exception('Content directory not found! Check your configuration.');
		}

		if (!is_writeable($this->config['output_dir']))
		{
			throw new Site_Builder_Exception('Output directory not writeable. Check your directory permissions.');
		}
	}
	
	// Factory method for creating a Site_Builder object from a config file
	public static function load($config_file)
	{
		$config = parse_ini_file($config_file);
		return new Site_Builder($config);
	}
	

	public function renderSite()
	{
		$files = $this->getFiles($this->config['content_dir']);
		foreach($files as $file)
		{
			$output_filename = $this->getOutputFilename($file);
			$output = $this->renderFile($file);
			file_put_contents($output_filename, $output);
		}
	}
	
	public function getOutputFilename($file)
	{
		return sprintf(
			'%s/%s%s', 
			$this->config['output_dir'], 
			pathinfo($file, PATHINFO_FILENAME), 
			$this->config['output_extension']
		);
	}
	
	public function renderFile($file)
	{
		
		$view = new Site_Builder_Template();
		$view->template = $this->config['default_template'];
		
		// Capture output
		ob_start();
		require($file);
		$view->content = ob_get_clean();

		// Return rendered view
	    return $view->render($view->template);
	}
	
	public function getFiles($directory)
	{
		$dir = new DirectoryIterator($directory);
		$files = array();
		foreach($dir as $file)
		{
			if (!$file->isFile())
			{
				continue;
			}
			
			$files[] = $file->getPathname(); 
		}
		return $files;
	}
}

/* Credit (but no blame) to Chad Emrys Minick for this Template pattern */
class Site_Builder_Template {
    private $vars  = array(
    );

    public function __get($name) {
        return $this->vars[$name];
    }
 
    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }
 
    public function render($__file) {
        extract($this->vars, EXTR_SKIP);
        ob_start();
        include($__file);
        return ob_get_clean();
    }
}


class Site_Builder_Exception extends Exception { }
