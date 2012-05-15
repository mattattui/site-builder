<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->in($dir = '/Users/matt/Projects/site-builder')
    ->ignoreVCS(true)
    ->name('*.php')
    ->exclude('doc')
    ->exclude('Tests')
    ->exclude('test')
    ->exclude('tests')
    ->exclude('templates')
    ->exclude('content')
    ->exclude('output')
    ->exclude('build')
    ->exclude('vendor')
    ->notName('compile.php')
;

return new Sami($iterator, array(
    'title'               => 'Site-builder (master)',
    'theme'               => 'enhanced',
    'build_dir'           => __DIR__.'/api',
    'cache_dir'           => __DIR__.'/../../sami/cache/site-builder',
    'include_parent_data' => false,
));
