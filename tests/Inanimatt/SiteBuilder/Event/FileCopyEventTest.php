<?php

namespace Inanimatt\SiteBuilder\Event;

use \Mockery as m;

class FileCopyEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileCopyEvent
     */
    protected $object;

    /**
     * @var string
     */
    protected $infile;

    /**
     * @var string
     */
    protected $outfile;
    
    protected function setUp()
    {
        $this->infile = tempnam(sys_get_temp_dir(), 'sl');
        $this->outfile = tempnam(sys_get_temp_dir(), 'sl');
        unlink($this->infile);
        unlink($this->outfile);
        $this->infile .= '.md';
        file_put_contents($this->infile, 'Lorem ipsum dolor sit amet.');
        
        $this->object = new FileCopyEvent($this->infile, $this->outfile);
    }

    protected function tearDown()
    {
        unlink($this->infile);
    }

    public function testGetSource()
    {
        $this->assertEquals($this->object->getSource(), $this->infile);
    }

    public function testSetTarget()
    {
        $this->object->setTarget('lorem ipsum');
        $this->assertEquals($this->object->getTarget(), 'lorem ipsum');
    }

    public function testGetExtension()
    {
        $this->assertEquals($this->object->getExtension(), 'md');
    }

    public function testIsModifiedFalseWhenNotModified()
    {
        $this->assertFalse($this->object->isModified());
    }

    public function testIsModifiedTrueWhenModified()
    {
        $this->object->setIsModified(true);
        $this->assertTrue($this->object->isModified());
    }

    public function testGetContentReadsFile()
    {
        $this->assertEquals($this->object->getContent(), 'Lorem ipsum dolor sit amet.');
    }

    public function testsetContent()
    {
        $this->object->setContent('Something else.');
        $this->assertEquals($this->object->getContent(), 'Something else.');
        $this->assertTrue($this->object->isModified());
    }
}