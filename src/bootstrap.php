<?php
require_once __DIR__.'/../vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
));
$loader->registerPrefixes(array(
    'Twig_' => __DIR__.'/../vendor/twig/twig/lib',
));
$loader->register();

// Utility function - shortcut to htmlspecialchars().
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Set up the service container
$sc = new ContainerBuilder;
$loader = new YamlFileLoader($sc, new FileLocator(array(__DIR__, __DIR__.'/..')));
$loader->load('services.yml');

return $sc;
