<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class TwigHtmlTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigHtmlTransformer
     */
    protected $object;

    protected $workspace;

    protected function setUp()
    {
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    protected function tearDown()
    {
        $this->clean($this->workspace);
    }

    /**
     * @param string $file
     */
    private function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }

            rmdir($file);
        } else {
            unlink($file);
        }
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

    public function testTransform()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.test';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';
        
        $content = '---
title: Lorem ipsum
---
ORIGINAL INPUT';
        file_put_contents($sourceFilePath, $content);

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

        $object = new TwigHtmlTransformer($reader, $twig, 'whatever');
        $object->transform($sourceFilePath, $targetFilePath);
        $output = file_get_contents($targetFilePath);

        $this->assertEquals($output, 'TRANSFORMED OUTPUT');
    }

    public function testTransformOverridesTemplate()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.test';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';
        
        $content = '---
title: Lorem ipsum
template: overridden.twig
---
ORIGINAL INPUT';
        file_put_contents($sourceFilePath, $content);

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

        $object = new TwigHtmlTransformer($reader, $twig, 'whatever');
        $object->transform($sourceFilePath, $targetFilePath);
        $output = file_get_contents($targetFilePath);

        $this->assertEquals($output, 'TRANSFORMED OUTPUT');
    }
}