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
            ->with('event_dispatcher')
            ->andReturn(false)
            ->mock();
        
        $this->assertNull($this->object->process($container));
    }
    
    public function testProcessAddsServices()
    {
        $service = m::mock('mock_service')
            ->shouldReceive('addMethodCall')
            ->times(2)
            ->with('addListener', m::type('array'))
            ->mock();

        $container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->shouldReceive('hasDefinition')
            ->with('event_dispatcher')
            ->andReturn(true)
            ->shouldReceive('getDefinition')
            ->with('event_dispatcher')
            ->andReturn($service)
            ->shouldReceive('findTaggedServiceIds')
            ->with('sitebuilder.transformer')
            ->andReturn(array(
                'id1' => array('priority' => 0),
                'id2' => array('priority' => 127),
            ))
            ->mock();
        
        $this->assertNull($this->object->process($container));
    }


}