<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class TwigHtmlTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigHtmlTransformer
     */
    protected $object;



    public function testTransformIgnoresNonHTML()
    {
        
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

        $twig = m::mock('\Twig_Environment')
            ->shouldReceive('render')
            ->with('whatever', array('title' => 'Lorem ipsum', 'content' => 'ORIGINAL INPUT'))
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('html')
            ->shouldReceive('getContent')
            ->andReturn($content)
            ->shouldReceive('setContent')
            ->with('TRANSFORMED OUTPUT')
            ->mock();

        $object = new TwigHtmlTransformer($reader, $twig, 'whatever');
        $object->transform($event);
    }



    public function testTransformOverridesTemplate()
    {
        $content = '---
title: Lorem ipsum
template: overridden.twig
---
ORIGINAL INPUT';

        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader')
            ->shouldReceive('parse')
            ->with($content)
            ->andReturn(array('ORIGINAL INPUT', array('title' => 'Lorem ipsum', 'template' => 'overridden.twig')))
            ->mock();

        $twig = m::mock('\Twig_Environment')
            ->shouldReceive('render')
            ->with('overridden.twig', array('title' => 'Lorem ipsum', 'content' => 'ORIGINAL INPUT', 'template' => 'overridden.twig'))
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('html')
            ->shouldReceive('getContent')
            ->andReturn($content)
            ->shouldReceive('setContent')
            ->with('TRANSFORMED OUTPUT')
            ->mock();

        $object = new TwigHtmlTransformer($reader, $twig, 'whatever');
        $object->transform($event);
    }

}