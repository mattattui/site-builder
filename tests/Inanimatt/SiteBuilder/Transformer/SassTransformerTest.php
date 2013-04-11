<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;
use Symfony\Component\Process\Process;

class SassProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SassProcessBuilder
     */
    protected $object;

    public function testIsInstalledSucceeds()
    {
        // Safe to assume phpunit binary is installed…?
        $object = new SassProcessBuilder($_SERVER['_']);
        $this->assertTrue($object->isInstalled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidExecutable()
    {
        // Safe to assume PHP binary is installed…?
        $object = new SassProcessBuilder('NoSuchFile');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidStyle()
    {
        // Safe to assume phpunit binary is installed…?
        $object = new SassProcessBuilder($_SERVER['_'], 'NoSuchStyle');
    }

    public function testGetProcess()
    {
        // Safe to assume PHP binary is installed…?
        $object = new SassProcessBuilder($_SERVER['_']);
        $process = $object->getProcess('filename.scss');
        
        $this->assertTrue($process instanceof Process);
        $this->assertTrue(strpos($process->getCommandLine(), '--scss') !== false);
    }

    public function testGetSassProcess()
    {
        // Safe to assume PHP binary is installed…?
        $object = new SassProcessBuilder($_SERVER['_']);
        $process = $object->getProcess('filename.sass');
        
        $this->assertTrue($process instanceof Process);
        $this->assertTrue(strpos($process->getCommandLine(), '--scss') === false);
    }

}
