<?php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Inanimatt\SiteBuilder\TransformerCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Inanimatt\\SiteBuilder', __DIR__.'/../src');
$loader->register();

// Utility function - shortcut to htmlspecialchars().
if (!function_exists('e')) {
    function e($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// Set up the service container
$sc = new ContainerBuilder;
$sc->addCompilerPass(new TransformerCompilerPass);

$searchPath = array(__DIR__, __DIR__.'/..');
if (defined('SITEBUILDER_ROOT')) {
    array_unshift($searchPath, SITEBUILDER_ROOT);
}

$locator = new FileLocator($searchPath);
$resolver = new LoaderResolver(array(
    new YamlFileLoader($sc, $locator),
    new IniFileLoader($sc, $locator),
));

$loader = new DelegatingLoader($resolver);
$loader->load('services.yml');

return $sc;
