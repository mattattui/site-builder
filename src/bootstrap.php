<?php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Inanimatt\SiteBuilder\TransformerCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;

(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

$file = defined('SITEBUILDER_ROOT') ? SITEBUILDER_ROOT.'/cache/container.php' : __DIR__ .'/../cache/container.php';
if (!file_exists(dirname($file))) {
    mkdir(dirname($file));
}

$searchPath = array(getcwd(), __DIR__, __DIR__.'/..');
if (defined('SITEBUILDER_ROOT')) {
    array_unshift($searchPath, SITEBUILDER_ROOT);
}

if (file_exists($file)) {
    require_once $file;
    $container = new ProjectServiceContainer();
} else {
    // Set up the service container
    $container = new ContainerBuilder;
    $container->addCompilerPass(new TransformerCompilerPass);

    $locator = new FileLocator($searchPath);
    $resolver = new LoaderResolver(array(
        new YamlFileLoader($container, $locator),
        new IniFileLoader($container, $locator),
    ));

    $loader = new DelegatingLoader($resolver);
    $loader->load('services.yml');

    $container->compile();
    
    $dumper = new PhpDumper($container);
    file_put_contents($file, $dumper->dump());
}

return $container;