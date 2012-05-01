<?php
require_once __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Inanimatt\SiteBuilder\SiteBuilderException;

$loader = new UniversalClassLoader;
$loader->registerNamespaces(array(
    'Inanimatt\\SiteBuilder'                  => __DIR__ . '/../src',
    'dflydev\\markdown'                       => __DIR__ . '/../vendor/dflydev/markdown/src',
    'Symfony\\Component\\Yaml'                => __DIR__ . '/../vendor/symfony/yaml/',
    'Symfony\\Component\\Finder'              => __DIR__ . '/../vendor/symfony/finder/',
    'Symfony\\Component\\DependencyInjection' => __DIR__ . '/../vendor/symfony/dependency-injection/',
    'Symfony\\Component\\ClassLoader'         => __DIR__ . '/../vendor/symfony/class-loader/',
    'Symfony\\Component\\Config'              => __DIR__ . '/../vendor/symfony/config/',
    'Symfony\\Component\\Console'             => __DIR__ . '/../vendor/symfony/console/',
));
$loader->registerPrefixes(array(
    'Twig_' => __DIR__.'/../vendor/twig/twig/lib',
));
$loader->register();

// Utility function - shortcut to htmlspecialchars().
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// Set up the service container
$sc = new ContainerBuilder;

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
