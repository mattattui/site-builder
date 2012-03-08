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
	
	protected $twig = null;
	protected $yaml = null;
	protected $markdown = null;
	
	
	public function __construct($config = array())
	{
		if (is_file(__DIR__.'/vendor/.composer/autoload.php'))
		{
			require __DIR__.'/vendor/.composer/autoload.php';
			
			if (class_exists('Twig_Loader_Filesystem', true))
			{
				$loader = new Twig_Loader_Filesystem(__DIR__);
				$this->twig = new Twig_Environment($loader);
			}
			
			if (class_exists('Symfony\Component\Yaml\Parser', true))
			{
				$this->yaml = new Symfony\Component\Yaml\Parser;
			}
			
			if (class_exists('dflydev\markdown\MarkdownParser', true))
			{
				$this->markdown = new dflydev\markdown\MarkdownParser;
			}
			
		}
		
		$this->config = array_merge($this->config, $config);
		
		$this->checkConfiguration();
	}
	
	public function checkConfiguration($config = null)
	{
		if (is_null($config))
		{
			$config = $this->config;
		}
		
		if (!is_array($config))
		{
			throw new Site_Builder_Exception('Invalid or missing configuration.');
		}

		if (!isset($config['default_template']) || !is_file($config['default_template']))
		{
			throw new Site_Builder_Exception('Default template not found! Check your configuration.');
		}

		if (!isset($config['output_dir']) || !is_dir($config['output_dir']))
		{
			throw new Site_Builder_Exception('Output directory not found! Check your configuration.');
		}

		if (!isset($config['content_dir']) || !is_dir($config['content_dir']))
		{
			throw new Site_Builder_Exception('Content directory not found! Check your configuration.');
		}

		if (!isset($config['output_dir']) || !is_writeable($config['output_dir']))
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
		// Handle content and data first
		$file_info = new SplFileInfo($file);
		if ($file_info->getExtension() == 'md')
		{
			if (!$this->yaml)
			{
				throw new Site_Builder_Exception('Markdown file found, but Yaml component not installed');
			}
			if (!$this->markdown)
			{
				throw new Site_Builder_Exception('Markdown file found, but Markdown component not installed');
			}
			
			// Parse Markdown template
			
			$file_content = file_get_contents($file);

			// Search for front-matter, parse it, and then remove it
			$data = array();
			$front_matter = $this->getFrontMatter($file_content);
			$data = $this->yaml->parse($front_matter);
			
			/* Strip (from the start of the string) the length of the front-matter 
			 * collected, plus the size of the delimiters, and the starting and 
			 * ending line-breaks
			 */
			$file_content = substr($file_content, mb_strlen($front_matter) + 6 + (2 * strlen(PHP_EOL)));
			
			
			/* Parse remaining file as markdown */
			$data['content'] = $this->markdown->transformMarkdown($file_content);
			
			$template = isset($data['template']) ? $data['template'] : $this->config['default_template'];
			
			// Create a view so that rendering will still work
			$view = new Site_Builder_Template();
			$view->__setVars($data);
		
		} 
		elseif ($file_info->getExtension() == 'php')
		{
			$view = new Site_Builder_Template();
			$view->template = $this->config['default_template'];
		
			// Capture output
			ob_start();
			require($file);
			$view->content = ob_get_clean();
			
			$data = $view->__getVars();
			$template = $view->template;
		}
		


		// Insert content into template
		$template_file = new SplFileInfo($template);
		if ($template_file->getExtension() == 'twig')
		{
			if (!$this->twig)
			{
				throw new Site_Builder_Exception('Twig template give, but Twig not found.');
			}
			
			return $this->twig->render($template, $data);
		}
		
		// Return rendered view
		
		return $view->render($template);
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
	
	
	public function getFrontMatter($content)
	{
		if (preg_match('/^\-\-\-$(.*)^\-\-\-$/sm', $content, $matches))
		{
			return $matches[1];
		}
		
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

		public function __getVars() {
				return $this->vars;
		}

		public function __setVars($vars) {
				$this->vars = $vars;
		}
 
    public function render($__file) {
        extract($this->vars, EXTR_SKIP);
        ob_start();
        include($__file);
        return ob_get_clean();
    }
}


class Site_Builder_Exception extends Exception { }
