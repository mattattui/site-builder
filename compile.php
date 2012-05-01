<?php
require_once __DIR__.'/vendor/.composer/autoload.php';
use Symfony\Component\Finder\Finder;

@unlink('sitebuilder.phar');

$finder = new Finder;
$finder->files()
  ->ignoreVCS(true)
  ->name('*.php')
  ->name('*.twig')
  ->name('*.ini')
  ->name('*.yml')
  ->exclude('Tests')
  ->exclude('test')
  ->exclude(__DIR__.'/content')
  ->exclude(__DIR__.'output')
  ->exclude(__DIR__.'build')
  ->exclude('vendor/EHER')
  ->notName('phpunit.xml.dist')
  ->notName('compile.php')
  ->in(__DIR__)
;
  
$phar = new Phar('sitebuilder.phar', 0, 'sitebuilder.phar');
// $phar = $phar->convertToExecutable(Phar::TAR, Phar::GZ);
$phar->setSignatureAlgorithm(Phar::SHA1);

$phar->startBuffering();

foreach($finder as $file)
{
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
require_once 'phar://sitebuilder.phar/src/bootstrap.php';
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
rename('sitebuilder.phar', 'build/sitebuilder.phar');
