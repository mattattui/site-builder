<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class SassTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SassTransformer
     */
    protected $object;

    public function testTransformIgnoresNonSass()
    {
        $process_builder = m::mock('Inanimatt\SiteBuilder\Transformer\SassProcessBuilder');
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('notsass')
            ->mock();

        $object = new SassTransformer($process_builder);
        $object->transform($event);
    }

    public function testTransformSkipsIfNotInstalled()
    {
        $process_builder = m::mock('Inanimatt\SiteBuilder\Transformer\SassProcessBuilder')
            ->shouldReceive('isInstalled')
            ->andReturn(false)
            ->mock();
        
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('scss')
            ->mock();

        $object = new SassTransformer($process_builder);
        $object->transform($event);
    }

    public function testTransform()
    {
        $process = m::mock('process')
            ->shouldReceive('run')
            ->shouldReceive('getOutput')
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $process_builder = m::mock('Inanimatt\SiteBuilder\Transformer\SassProcessBuilder')
            ->shouldReceive('isInstalled')
            ->andReturn(true)
            ->shouldReceive('getProcess')
            ->with('filename.scss')
            ->andReturn($process)
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('scss')
            ->shouldReceive('getTarget')
            ->andReturn('filename.scss')
            ->shouldReceive('setTarget')
            ->with('filename.css')
            ->shouldReceive('getSource')
            ->andReturn('filename.scss')
            ->shouldReceive('setContent')
            ->with('TRANSFORMED OUTPUT')
            ->mock();

        $object = new SassTransformer($process_builder);
        $object->transform($event);
    }
}
