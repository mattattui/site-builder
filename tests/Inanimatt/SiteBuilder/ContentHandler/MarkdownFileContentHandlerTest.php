<?php
namespace Inanimatt\SiteBuilder\ContentHandler;

class MarkdownFileContentHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MarkdownFileContentHandler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MarkdownFileContentHandler(__DIR__.'/../../../resources/subdir/example.md', 'subdir', 'subdir/example.md' );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::__construct
     */
    public function testConstructor()
    {
        try {
            $object = new MarkdownFileContentHandler(__DIR__.'/non-existent file', 'subdir', 'subdir/example.md' );
            $this->fail('->__construct() throws a SiteBuilderException if the file does not exist');
        } catch(\Exception $e) {
            $this->assertInstanceOf('Inanimatt\SiteBuilder\SiteBuilderException', $e, '->__construct() throws a SiteBuilderException if the file does not exist');
            $this->assertEquals('File not found.', $e->getMessage(), '->__construct() throws a SiteBuilderException if the file does not exist');
        }
    }

    /**
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::getName
     */
    public function testGetName()
    {
        $this->assertEquals($this->object->getName(), 'subdir/example.md');
    }

    /**
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::getContent
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::__construct
     */
    public function testGetContent()
    {
        $content = '<h1>Hello World</h1>

<p>Lorem ipsum dolor sit <em>amet</em>, consectetur adipisicing elit, <a href="http://www.example.com/">sed do eiusmod tempor</a> incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco <strong>laboris nisi</strong> ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
';

        $this->assertEquals($content, $this->object->getContent());
    }

    /**
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::getMetadata
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::getFrontMatter
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::__construct
     */
    public function testGetMetadata()
    {
        $this->assertTrue(is_array($this->object->getMetadata()));
        $this->assertArrayHasKey('title', $this->object->getMetadata());
        $this->assertArrayHasKey('content', $this->object->getMetadata());
        $m = $this->object->getMetadata();
        $this->assertEquals('Hello World & Stuff', $m['title']);
        $this->assertEquals('template.twig', $m['template']);
       
        $o = new MarkdownFileContentHandler(__DIR__.'/../../../resources/subdir/example-nofm.md', 'subdir', 'subdir/example.md' );
        
    }

    /**
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::getMetadata
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::getFrontMatter
     * @covers Inanimatt\SiteBuilder\ContentHandler\MarkdownFileContentHandler::__construct
     */
    public function testGetMetadataBlank()
    {
        $o = new MarkdownFileContentHandler(__DIR__.'/../../../resources/subdir/example-nofm.md', 'subdir', 'subdir/example-nofm.md' );

        $this->assertTrue(is_array($o->getMetadata()), '->getMetadata() returns array, even if no frontmatter');
        $this->assertArrayHasKey('content', $o->getMetadata(), '->getMetadata() with no frontmatter contains content var');
        $this->assertCount(1, $o->getMetadata(), '->getMetadata() with no frontmatter contains only content var');
        
    }
}
