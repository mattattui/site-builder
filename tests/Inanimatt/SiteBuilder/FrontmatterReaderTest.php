<?php

namespace Inanimatt\SiteBuilder;

use \Mockery as m;
use Symfony\Component\Yaml\Parser;

class FrontmatterReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FrontmatterReader
     */
    protected $object;

    protected function setUp()
    {
        $yaml = new Parser;
        $this->object = new FrontmatterReader($yaml);
    }

    protected function tearDown()
    {
    }

    public function testParseReturnsFrontmatter()
    {
        $testContent ='---
title: Hello world
---
Lorem ipsum dolor sit amet.';

        list($content, $data) = $this->object->parse($testContent);
        
        $this->assertEquals('Lorem ipsum dolor sit amet.', $content);
        $this->assertEquals(array('title' => 'Hello world'), $data);
    }

    public function testParseReturnsWithoutFrontmatter()
    {
        $testContent ='Lorem ipsum dolor sit amet.';

        list($content, $data) = $this->object->parse($testContent);
        
        $this->assertEquals('Lorem ipsum dolor sit amet.', $content);
        $this->assertEquals(array(), $data);
    }
}