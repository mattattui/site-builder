<?php

namespace Inanimatt\SiteBuilder;

use \Mockery as m;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Inanimatt\SiteBuilder\Event\FileCopyEvent;

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
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    protected function tearDown()
    {
        $this->clean($this->workspace);
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

    public function testCopyCallsDispatcher()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.test';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';
    
        file_put_contents($sourceFilePath, 'ORIGINAL INPUT');
    
        $filesystem = $this->getDefaultMock($sourceFilePath, $targetFilePath);

        $filesystem->copy($sourceFilePath, $targetFilePath);
    }

    public function testCopyWritesModified()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.test';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';
    
        file_put_contents($sourceFilePath, 'ORIGINAL INPUT');
    
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getSource')
            ->andReturn($sourceFilePath)
            ->shouldReceive('getTarget')
            ->andReturn($targetFilePath)
            ->shouldReceive('isModified')
            ->andReturn(true)
            ->shouldReceive('getContent')
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $dispatcher = m::mock('Symfony\Component\EventDispatcher\EventDispatcher')
            ->shouldReceive('dispatch')
            ->with(FilesystemEvents::COPY, m::type('Inanimatt\SiteBuilder\Event\FileCopyEvent'))
            ->andReturn($event)
            ->mock();

        $filesystem = new TransformingFilesystem($dispatcher);
        $filesystem->copy($sourceFilePath, $targetFilePath);

        $this->assertEquals(file_get_contents($targetFilePath), 'TRANSFORMED OUTPUT');
    }

    // Tests imported from (and written by Symfony Filesystem component)
    public function testCopyCreatesNewFile()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';
    
        file_put_contents($sourceFilePath, 'SOURCE FILE');
    
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getSource')
            ->andReturn($sourceFilePath)
            ->shouldReceive('getTarget')
            ->andReturn($targetFilePath)
            ->shouldReceive('isModified')
            ->andReturn(false)
            ->mock();

        $dispatcher = m::mock('Symfony\Component\EventDispatcher\EventDispatcher')
            ->shouldReceive('dispatch')
            ->with(FilesystemEvents::COPY, m::type('Inanimatt\SiteBuilder\Event\FileCopyEvent'))
            ->andReturn($event)
            ->mock();

        $filesystem = new TransformingFilesystem($dispatcher);

        $filesystem->copy($sourceFilePath, $targetFilePath);
    
        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }
    
    public function getDefaultMock($sourceFilePath, $targetFilePath)
    {
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getSource')
            ->andReturn($sourceFilePath)
            ->shouldReceive('getTarget')
            ->andReturn($targetFilePath)
            ->shouldReceive('isModified')
            ->andReturn(false)
            ->mock();

        $dispatcher = m::mock('Symfony\Component\EventDispatcher\EventDispatcher')
            ->shouldReceive('dispatch')
            ->with(FilesystemEvents::COPY, m::type('Inanimatt\SiteBuilder\Event\FileCopyEvent'))
            ->andReturn($event)
            ->mock();

        $filesystem = new TransformingFilesystem($dispatcher);

        return $filesystem;
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\IOException
     */
    public function testCopyFails()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';
    
        $filesystem = $this->getDefaultMock($sourceFilePath, $targetFilePath);
        $filesystem->copy($sourceFilePath, $targetFilePath);
    }

    public function testCopyOverridesExistingFileIfModified()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_target_file';
    
        file_put_contents($sourceFilePath, 'SOURCE FILE');
        file_put_contents($targetFilePath, 'TARGET FILE');
        touch($targetFilePath, time() - 1000);
    

        $filesystem = $this->getDefaultMock($sourceFilePath, $targetFilePath);
        $filesystem->copy($sourceFilePath, $targetFilePath);
    
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
    
        $filesystem = $this->getDefaultMock($sourceFilePath, $targetFilePath);
        $filesystem->copy($sourceFilePath, $targetFilePath);
    
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
    
        $filesystem = $this->getDefaultMock($sourceFilePath, $targetFilePath);
        $filesystem->copy($sourceFilePath, $targetFilePath, true);
    
        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }
    
    public function testCopyCreatesTargetDirectoryIfItDoesNotExist()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'copy_source_file';
        $targetFileDirectory = $this->workspace.DIRECTORY_SEPARATOR.'directory';
        $targetFilePath = $targetFileDirectory.DIRECTORY_SEPARATOR.'copy_target_file';
    
        file_put_contents($sourceFilePath, 'SOURCE FILE');
    
        $filesystem = $this->getDefaultMock($sourceFilePath, $targetFilePath);
        $filesystem->copy($sourceFilePath, $targetFilePath);
    
        $this->assertTrue(is_dir($targetFileDirectory));
        $this->assertFileExists($targetFilePath);
        $this->assertEquals('SOURCE FILE', file_get_contents($targetFilePath));
    }

}

class TestTransformer implements TransformerInterface
{
    public function transform(FileCopyEvent $event)
    {
        $event->setContent(str_replace('ORIGINAL', 'TRANSFORMED', $event->getContent()));
    }
}