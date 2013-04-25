<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class FrontmatterTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigHtmlTransformer
     */
    protected $object;



    public function testTransformIgnoresNonHTML()
    {
        $content = 'Who cares';
        
        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader')
            ->shouldReceive('parse')
            ->with($content)
            ->andReturn(array('ORIGINAL INPUT', array('title' => 'Lorem ipsum')))
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('nothtml')
            ->mock();

        $object = new FrontmatterTransformer($reader);
        $object->transform($event);
    }

    public function testTransform()
    {
        $content = '---
title: Lorem ipsum
---
ORIGINAL INPUT';

        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader')
            ->shouldReceive('parse')
            ->with($content)
            ->andReturn(array('ORIGINAL INPUT', array('title' => 'Lorem ipsum')))
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('html')
            ->shouldReceive('getContent')
            ->andReturn($content)
            ->shouldReceive('setContent')
            ->with('ORIGINAL INPUT')
            ->mock();

        $event->data = new \PhpCollection\Map;

        $object = new FrontmatterTransformer($reader);
        $object->transform($event);

        $this->assertEquals($event->data->get('title')->get(), 'Lorem ipsum');
    }

    public function testNofrontmatter()
    {
        $content = 'ORIGINAL INPUT';

        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader')
            ->shouldReceive('parse')
            ->with($content)
            ->andReturn(array('ORIGINAL INPUT', array()))
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('html')
            ->shouldReceive('getContent')
            ->andReturn($content)
            ->shouldReceive('setContent')
            ->with('ORIGINAL INPUT')
            ->mock();

        $event->data = new \PhpCollection\Map;

        $object = new FrontmatterTransformer($reader);
        $object->transform($event);
    }
}