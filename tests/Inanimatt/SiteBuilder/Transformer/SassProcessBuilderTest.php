<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;
use Symfony\Component\Process\Process;

class SassProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $exe;

    public function setUp()
    {
        $this->exe = tempnam(sys_get_temp_dir(), 'sh');
        chmod($this->exe, 0777);
    }
    
    public function tearDown()
    {
        unlink($this->exe);
    }

    public function testIsInstalledSucceeds()
    {
        // Safe to assume shell is installed?
        $object = new SassProcessBuilder($this->exe);
        $this->assertTrue($object->isInstalled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidExecutable()
    {
        // Safe to assume shell is installed?
        $object = new SassProcessBuilder('NoSuchFile');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidStyle()
    {
        // Safe to assume shell is installed?
        $object = new SassProcessBuilder($this->exe, 'NoSuchStyle');
    }

    public function testGetProcess()
    {
        // Safe to assume shell is installed?
        $object = new SassProcessBuilder($this->exe);
        $process = $object->getProcess('filename.scss');
        
        $this->assertTrue($process instanceof Process);
        $this->assertTrue(strpos($process->getCommandLine(), '--scss') !== false);
    }

    public function testGetSassProcess()
    {
        // Safe to assume PHP binary is installed…?
        $object = new SassProcessBuilder($this->exe);
        $process = $object->getProcess('filename.sass');
        
        $this->assertTrue($process instanceof Process);
        $this->assertTrue(strpos($process->getCommandLine(), '--scss') === false);
    }

}
