<?php

namespace Inanimatt\SiteBuilder;

use \Mockery as m;

class TransformingFilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransformingFilesystem
     */
    protected $object;

    /**
     * @covers Inanimatt\SiteBuilder\TransformingFilesystem::__construct
     */
    protected function setUp()
    {
        $this->object = new TransformingFilesystem;
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @covers Inanimatt\SiteBuilder\TransformingFilesystem::addTransformer
     */
    public function testAddTransformer()
    {
        $transformer = m::mock('Inanimatt\SiteBuilder\Transformer\TransformerInterface')
            ->shouldReceive('getSupportedExtensions')
            ->andReturn(array('test'))
            ->mock();
        
        $this->object->addTransformer($transformer);
    }

    /**
     * @covers Inanimatt\SiteBuilder\TransformingFilesystem::setLogger
     */
    public function testSetLogger()
    {
        $logger = m::mock('Psr\Log\LoggerInterface');
        $this->object->setLogger($logger);
    }

}