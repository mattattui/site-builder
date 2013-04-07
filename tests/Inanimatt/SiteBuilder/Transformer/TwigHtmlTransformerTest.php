<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class TwigHtmlTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigHtmlTransformer
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
        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader');
        $twig = m::mock('\Twig_Environment');
        $object = new TwigHtmlTransformer($reader, $twig, 'whatever');
        
        $extensions = $object->getSupportedExtensions();
        $this->assertContains('html', $extensions);
        $this->assertContains('htm', $extensions);
    }
}