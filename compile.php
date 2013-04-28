<?php
require_once __DIR__.'/vendor/autoload.php';
use Symfony\Component\Finder\Finder;

$finder = new Finder;
$finder->files()
  ->ignoreVCS(true)
  ->name('*.php')
  ->name('*.twig')
  ->name('*.yml')
  ->name('*.ini')
  ->exclude('doc')
  ->exclude('Tests')
  ->exclude('test')
  ->exclude('tests')
  ->exclude('templates')
  ->exclude('content')
  ->exclude('output')
  ->exclude('build')
  ->exclude('vendor/phpunit')
  ->exclude('vendor/mockery')
  ->notName('phpunit.xml.dist')
  ->notName('compile.php')
  ->in(__DIR__)
;

$phar = new Phar('sitebuilder.phar', 0, 'sitebuilder.phar');
$phar = $phar->convertToExecutable(Phar::TAR, Phar::GZ);
$phar->setSignatureAlgorithm(Phar::SHA1);

$phar->startBuffering();

foreach ($finder as $file) {
  $path = str_replace(__DIR__.DIRECTORY_SEPARATOR, '', $file->getRealPath());
  $content = file_get_contents($file);
  $phar->addFromString($path, $content);
  echo $path.PHP_EOL;

}

// $phar->setMetadata(array('bootstrap' => 'index.php'));

$phar->setStub(<<<'EOF'
<?php
Phar::mapPhar('sitebuilder.phar');
define('SITEBUILDER_ROOT', __DIR__);

// Create config file if not found
// FIXME: This is a kludge. Should be able to have one in the phar, overridden by one outside
// if (!is_file(SITEBUILDER_ROOT.'/config.ini')) {
//     file_put_contents(SITEBUILDER_ROOT.'/config.ini', '
// [parameters]
//
// ; To use the default Twig template, install Twig and change .php to .twig below
// template_path = templates/
// default_template = template.php
//
// ; Where to look for content files
// content_dir = content
//
// ; Where to put the generated site
// output_dir = output
// ');
// }

require_once 'phar://sitebuilder.phar/sitebuilder.php';

__HALT_COMPILER();
');
EOF
);

// save the phar archive to disk
$phar->stopBuffering();

if (!is_dir('build')) {
    mkdir('build');
}
rename('sitebuilder.phar.tar.gz', 'build/sitebuilder.phar');
