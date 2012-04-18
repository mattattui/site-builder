<?php

require_once __DIR__.'/vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Inanimatt\SiteBuilder\SiteBuilder;
use Inanimatt\SiteBuilder\SiteBuilderException;

$loader = new UniversalClassLoader;
$loader->registerNamespaces(array(
    'Inanimatt\\SiteBuilder'                  => __DIR__ . '/src',
    'dflydev\\markdown'                       => __DIR__ . '/vendor/dflydev/markdown/src',
    'Symfony\\Component\\Yaml'                => __DIR__ . '/vendor/symfony/yaml/',
    'Symfony\\Component\\Finder'              => __DIR__ . '/vendor/symfony/finder/',
    'Symfony\\Component\\DependencyInjection' => __DIR__ . '/vendor/symfony/dependency-injection/',
    'Symfony\\Component\\ClassLoader'         => __DIR__ . '/vendor/symfony/class-loader/',
));
$loader->registerPrefixes(array(
    'Twig_' => __DIR__.'/vendor/twig/twig/lib',
));
$loader->register();

// Utility function - shortcut to htmlspecialchars().
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}



// Get the config file from the command-line, or use the default
$config_file = isset($_SERVER['argv']) && isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'config.ini';

if (!is_file($config_file))
{
    throw new SiteBuilderException('Config file not found!');
}   

// Render the site with the given config file
try {
    $builder = SiteBuilder::load($config_file);
    $builder->renderSite();
} catch (SiteBuilderException $e) {
    die($e->getMessage().PHP_EOL);
}