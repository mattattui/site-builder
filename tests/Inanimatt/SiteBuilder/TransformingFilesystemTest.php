<?php

namespace Inanimatt\SiteBuilder;

use \Mockery as m;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;

class TransformingFilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransformingFilesystem
     */
    protected $filesystem;

    /**
     * @var string $workspace
     */
    private $workspace = null;
        
    private static $symlinkOnWindows = null;

    public static function setUpBeforeClass()
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            self::$symlinkOnWindows = true;
            $originDir = tempnam(sys_get_temp_dir(), 'sl');
            $targetDir = tempnam(sys_get_temp_dir(), 'sl');
            if (true !== @symlink($originDir, $targetDir)) {
                $report = error_get_last();
                if (is_array($report) && false !== strpos($report['message'], 'error code(1314)')) {
                    self::$symlinkOnWindows = false;
                }
            }
        }
    }
    
    protected function setUp()
    {
        $this->filesystem = new TransformingFilesystem;

        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    protected function tearDown()
    {
        $this->clean($this->workspace);
    }

    public function testAddTransformer()
    {
        $transformer = m::mock('Inanimatt\SiteBuilder\Transformer\TransformerInterface')
            ->shouldReceive('getSupportedExtensions')
            ->andReturn(array('test'))
            ->mock();
        
        $this->filesystem->addTransformer($transformer);
        $this->assertTrue($this->filesystem->hasTransformer('test'));
    }

    public function testSetLogger()
    {
        $logger = m::mock('Psr\Log\LoggerInterface');
        $this->filesystem->setLogger($logger);
    }

    /**
     * @param string $file
     */
    private function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }

            rmdir($file);
        } else {
            unlink($file);
        }
    }

    public function testCopyTransformsFile()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.test';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';

        file_put_contents($sourceFilePath, 'ORIGINAL INPUT');

        $transformer = new TestTransformer;
        $this->filesystem->addTransformer($transformer);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertFileExists($targetFilePath);
        $this->assertEquals('TRANSFORMED INPUT', file_get_contents($targetFilePath));
    }

    // Tests imported from (and written by Symfony Filesystem component)
    public function testCopyCreatesNewFile()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     */
    public function testCopyFails()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';

        $this->filesystem->copy($sourceFilePath, $targetFilePath);
    }

    public function testCopyOverridesExistingFileIfModified()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');
        touch($targetFilePath, time() - 1000);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }

    public function testCopyDoesNotOverrideExistingFileByDefault()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');

        // make sure both files have the same modification time
        $modificationTime = time() - 1000;
        touch($sourceFilePath, $modificationTime);
        touch($targetFilePath, $modificationTime);

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertFileExists($targetFilePath);
        $this->assertEquals('TARGET FILE', file_get_contents($targetFilePath));
    }

    public function testCopyOverridesExistingFileIfForced()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');

        // make sure both files have the same modification time
        $modificationTime = time() - 1000;
        touch($sourceFilePath, $modificationTime);
        touch($targetFilePath, $modificationTime);

        $this->filesystem->copy($sourceFilePath, $targetFilePath, true);

        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }

    public function testCopyCreatesTargetDirectoryIfItDoesNotExist()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFileDirectory = $this->workspace.DIRECTORY_SEPARATOR.'directory';
        $targetFilePath = $targetFileDirectory.DIRECTORY_SEPARATOR.'copy_target_file';

        file_put_contents($sourceFilePath, 'SOURCE FILE');

        $this->filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertTrue(is_dir($targetFileDirectory));
        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }

}

class TestTransformer implements TransformerInterface
{
    public function getSupportedExtensions()
    {
        return array('test');
    }

    public function transform($source, $target)
    {
        $input = file_get_contents($source);
        $content = str_replace('ORIGINAL', 'TRANSFORMED', $input);
        file_put_contents($target, $content);
    }
}