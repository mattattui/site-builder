<?php

require('SiteBuilder.php');


// Get the config file from the command-line, or use the default
$config_file = isset($_SERVER['argv']) && isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'config.ini';

if (!is_file($config_file))
{
	throw new Site_Builder_Exception('Config file not found!');
}	

// Render the site with the given config file
try {
	$builder = Site_Builder::load($config_file);
	$builder->renderSite();
} catch (Site_Builder_Exception $e) {
	die($e->getMessage().PHP_EOL);
}