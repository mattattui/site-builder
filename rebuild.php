<?php

require_once __DIR__.'/vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
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

// Set up the service container
$sc = new ContainerBuilder;
$sc->setParameter('sitebuilder.config', __DIR__.'/config.ini');

$sc->register('config', 'Inanimatt\SiteBuilder\SiteBuilderConfig')
    ->setFactoryClass('Inanimatt\SiteBuilder\SiteBuilderConfig')
    ->setFactoryMethod('load')
    ->addArgument('%sitebuilder.config%')
;

$sc->register('contentcollection', 'Inanimatt\SiteBuilder\FileContentCollection')
    ->addArgument($sc->get('config')->offsetGet('content_dir'))
;


// FIXME: This feels wrong, but I don't know what else to do
$sc->register('twig.loader', 'Twig_Loader_Filesystem')
    ->addArgument($sc->get('config')->offsetGet('template_path'));

$sc->register('twig', 'Twig_Environment')
    ->addArgument(new Reference('twig.loader'));

$sc->register('finder', '\Symfony\Component\Finder\Finder');
$sc->register('yaml', '\Symfony\Component\Yaml\Parser');
$sc->register('markdown', 'dflydev\markdown\MarkdownParser');

    
$sc->register('sitebuilder', 'Inanimatt\SiteBuilder\SiteBuilder')
    ->addArgument(new Reference('config'))
    ->addArgument(new Reference('twig'))
    ->addArgument(new Reference('yaml'))
    ->addArgument(new Reference('markdown'))
    ->addArgument(new Reference('contentcollection'))
;




// Get the config file from the command-line, or use the default
$config_file = isset($_SERVER['argv']) && isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'config.ini';

if (!is_file($config_file))
{
    throw new SiteBuilderException('Config file not found!');
}   

// Render the site with the given config file
try {
    $builder = $sc->get('sitebuilder');
    $builder->renderSite();
} catch (SiteBuilderException $e) {
    die($e->getMessage().PHP_EOL);
}