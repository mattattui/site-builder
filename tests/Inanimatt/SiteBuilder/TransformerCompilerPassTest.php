<?php

namespace Inanimatt\SiteBuilder;

use \Mockery as m;

class TransformerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransformerCompilerPass
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new TransformerCompilerPass;
    }

    protected function tearDown()
    {
    }

    public function testProcessReturnsIfServiceUndeclared()
    {
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->shouldReceive('hasDefinition')
            ->with('sitebuilder_filesystem')
            ->andReturn(false)
            ->mock();
        
        $this->assertNull($this->object->process($container));
    }

    public function testProcessAddsServices()
    {
        $service = m::mock('mock_service')
            ->shouldReceive('addMethodCall')
            ->times(2)
            ->with('addTransformer', m::type('array'))
            ->mock();

        $container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->shouldReceive('hasDefinition')
            ->with('sitebuilder_filesystem')
            ->andReturn(true)
            ->shouldReceive('getDefinition')
            ->with('sitebuilder_filesystem')
            ->andReturn($service)
            ->shouldReceive('findTaggedServiceIds')
            ->with('sitebuilder.transformer')
            ->andReturn(array(
                'id1' => 'service1',
                'id2' => 'service2',
            ))
            ->mock();
        
        $this->assertNull($this->object->process($container));
    }

}