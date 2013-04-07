<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class TwigMarkdownTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigMarkdownTransformer
     */
    protected $object;

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testgetSupportedExtensions()
    {
        $markdown = m::mock('dflydev\markdown\MarkdownParser');
        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader');
        $twig = m::mock('\Twig_Environment');
        $object = new TwigMarkdownTransformer($markdown, $reader, $twig, 'whatever');
        
        $extensions = $object->getSupportedExtensions();
        $this->assertContains('md', $extensions);
        $this->assertContains('markdown', $extensions);
    }
}