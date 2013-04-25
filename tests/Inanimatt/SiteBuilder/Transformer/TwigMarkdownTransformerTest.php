<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class TwigMarkdownTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigMarkdownTransformer
     */
    protected $object;

    public function testTransformIgnoresNonMarkdown()
    {
        $markdown = m::mock('dflydev\markdown\MarkdownParser');
        $twig = m::mock('\Twig_Environment');

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('notmarkdown')
            ->mock();

        $object = new TwigMarkdownTransformer($markdown, $twig, 'whatever');
        $object->transform($event);
    }

    public function testTransform()
    {
        $content = 'ORIGINAL INPUT';

        $markdown = m::mock('dflydev\markdown\MarkdownParser')
            ->shouldReceive('transformMarkdown')
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $twig = m::mock('\Twig_Environment')
            ->shouldReceive('render')
            ->with('whatever', array('title' => 'Lorem ipsum', 'content' => 'TRANSFORMED OUTPUT'))
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('md')
            ->shouldReceive('getContent')
            ->andReturn($content)
            ->shouldReceive('getTarget')
            ->andReturn('/path/to/file.md')
            ->shouldReceive('setTarget')
            ->andReturn('/path/to/file.html')
            ->shouldReceive('setContent')
            ->with('TRANSFORMED OUTPUT')
            ->mock();

        $event->data = new \PhpCollection\Map(array('title' => 'Lorem ipsum'));


        $object = new TwigMarkdownTransformer($markdown, $twig, 'whatever');
        $object->transform($event);
    }
    

    public function testTransformOverridesTemplate()
    {
        $content = '---
title: Lorem ipsum
---
ORIGINAL INPUT';

        $markdown = m::mock('dflydev\markdown\MarkdownParser')
            ->shouldReceive('transformMarkdown')
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $twig = m::mock('\Twig_Environment')
            ->shouldReceive('render')
            ->with('overridden.twig', array('title' => 'Lorem ipsum', 'content' => 'TRANSFORMED OUTPUT', 'template' => 'overridden.twig'))
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getExtension')
            ->andReturn('md')
            ->shouldReceive('getContent')
            ->andReturn($content)
            ->shouldReceive('getTarget')
            ->andReturn('/path/to/file.md')
            ->shouldReceive('setTarget')
            ->andReturn('/path/to/file.html')
            ->shouldReceive('setContent')
            ->with('TRANSFORMED OUTPUT')
            ->mock();

        $event->data = new \PhpCollection\Map(array('title' => 'Lorem ipsum', 'template' => 'overridden.twig'));

        $object = new TwigMarkdownTransformer($markdown, $twig, 'whatever');
        $object->transform($event);
    }

}
