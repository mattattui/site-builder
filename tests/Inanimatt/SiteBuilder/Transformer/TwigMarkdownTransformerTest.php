<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class TwigMarkdownTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigMarkdownTransformer
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
/*

    public function testTransform()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.md';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';
        
        $content = '---
title: Lorem ipsum
---
ORIGINAL INPUT';
        file_put_contents($sourceFilePath, $content);

        $markdown = m::mock('dflydev\markdown\MarkdownParser')
            ->shouldReceive('transformMarkdown')
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader')
            ->shouldReceive('parse')
            ->with($content)
            ->andReturn(array('ORIGINAL INPUT', array('title' => 'Lorem ipsum')))
            ->mock();

        $twig = m::mock('\Twig_Environment')
            ->shouldReceive('render')
            ->with('whatever', array('title' => 'Lorem ipsum', 'content' => 'TRANSFORMED OUTPUT'))
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $object = new TwigMarkdownTransformer($markdown, $reader, $twig, 'whatever');
        $object->transform($sourceFilePath, $targetFilePath);
        $output = file_get_contents($targetFilePath);

        $this->assertEquals($output, 'TRANSFORMED OUTPUT');
    }

    public function testTransformOverridesTemplate()
    {
        $sourceFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.md';
        $targetFilePath = $this->workspace.DIRECTORY_SEPARATOR.'example.html';
        
        $content = '---
title: Lorem ipsum
template: overridden.twig
---
ORIGINAL INPUT';
        file_put_contents($sourceFilePath, $content);

        $markdown = m::mock('dflydev\markdown\MarkdownParser')
            ->shouldReceive('transformMarkdown')
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $reader = m::mock('Inanimatt\SiteBuilder\FrontmatterReader')
            ->shouldReceive('parse')
            ->with($content)
            ->andReturn(array('ORIGINAL INPUT', array('title' => 'Lorem ipsum', 'template' => 'overridden.twig')))
            ->mock();

        $twig = m::mock('\Twig_Environment')
            ->shouldReceive('render')
            ->with('overridden.twig', array('title' => 'Lorem ipsum', 'content' => 'TRANSFORMED OUTPUT', 'template' => 'overridden.twig'))
            ->andReturn('TRANSFORMED OUTPUT')
            ->mock();

        $object = new TwigMarkdownTransformer($markdown, $reader, $twig, 'whatever');
        $object->transform($sourceFilePath, $targetFilePath);
        $output = file_get_contents($targetFilePath);

        $this->assertEquals($output, 'TRANSFORMED OUTPUT');
    }*/

}
