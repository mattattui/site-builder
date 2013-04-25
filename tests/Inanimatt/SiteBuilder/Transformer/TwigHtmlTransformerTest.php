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
        $twig = m::mock('\Twig_Environment');

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('nothtml')
            ->mock();

        $object = new TwigHtmlTransformer($twig, 'whatever');
        $object->transform($event);
    }


    public function testTransform()
    {
        $content = 'ORIGINAL INPUT';

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
        
        $event->data = new \PhpCollection\Map(array('title' => 'Lorem ipsum', 'content' => 'ORIGINAL INPUT'));

        $object = new TwigHtmlTransformer($twig, 'whatever');
        $object->transform($event);
    }





    public function testTransformOverridesTemplate()
    {
        $content = 'ORIGINAL INPUT';

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

        $event->data = new \PhpCollection\Map(array('title' => 'Lorem ipsum', 'content' => 'ORIGINAL INPUT', 'template' => 'overridden.twig'));

        $object = new TwigHtmlTransformer($twig, 'whatever');
        $object->transform($event);
    }


}