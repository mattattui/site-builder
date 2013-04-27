<?php

namespace Inanimatt\SiteBuilder\Transformer;

use \Mockery as m;

class PagecontextTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PagecontextTransformer
     */
    protected $object;


    public function testTransformRootPage()
    {
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getTarget')
            ->andReturn('output'.DIRECTORY_SEPARATOR.'index.html')
            ->mock();
            
        $event->data = new \PhpCollection\Map();
        $object = new PagecontextTransformer('output');
        $object->transform($event);
        
        $this->assertEquals($event->data->get('siteroot')->get() , '');
        $this->assertEquals(count($event->data->get('breadcrumbs')->get()) , 0);
        $this->assertEquals($event->data->get('pagename')->get() , 'index');
        
    }
    
    public function testTransformSectionPage()
    {
        $event = m::mock('Inanimatt\SiteBuilder\Event\FileCopyEvent')
            ->shouldReceive('getTarget')
            ->andReturn('output'.DIRECTORY_SEPARATOR.'section'.DIRECTORY_SEPARATOR.'subsection'.DIRECTORY_SEPARATOR.'content.html')
            ->mock();
            
        $event->data = new \PhpCollection\Map();
        $object = new PagecontextTransformer('output');
        $object->transform($event);
        
        $this->assertEquals($event->data->get('siteroot')->get() , '../../');
        $this->assertEquals(count($event->data->get('breadcrumbs')->get()) , 2);
        $this->assertEquals($event->data->get('pagename')->get() , 'content');
    }

}
