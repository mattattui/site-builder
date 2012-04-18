<?php
namespace Inanimatt\SiteBuilder;

/* Bulk template rendering class
 * 
 * Usage:
 *   $builder = new SiteBuilder($config_array);
 * or:
 *   $builder = SiteBuilder::load('config.ini');
 * then:
 *   $builder->renderSite();
 * 
 * renderSite() renders every file in the content directory and
 * saves it in the output directory with the output file extension.
 * 
 * You can also call renderFile($filename) to return the rendered
 * content of a single file, then display or save it yourself.
 */


class SiteBuilder
{
    protected $config   = null;
    protected $twig     = null;
    
    protected $contentCollection = null;
    protected $serialiser = null;
    
    
    public function __construct($config, $twig, ContentCollectionInterface $contentCollection, SerialiserInterface $serialiser)
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->contentCollection = $contentCollection;
        $this->serialiser = $serialiser;
    }
    
    
    // Get a list of content objects, run the renderer on each one, then serialise them
    // - content collection, content object
    // - renderer object
    // - serialiser
    public function renderSite()
    {
        $contentObjects = $this->contentCollection->getObjects();
        foreach($contentObjects as $content) {
            $output = $this->renderFile($content);
            
            $this->serialiser->write($output, $content->getName());
        }
    }
    
    
    public function renderFile(ContentObjectInterface $file)
    {
        // Handle content and data first
        $extension = $file->getExtension();
        
        $data = $file->getMetadata();
        $content = $file->getContent();
        
        if (!isset($data['template']) || !$data['template']) {
            $data['template'] = $this->config['default_template'];
        }
        
        // Insert content into template
        $templateInfo = new \splFileInfo($data['template']);
        if ($templateInfo->getExtension() == 'twig') {
            return $this->twig->render($data['template'], $data);
        }
        
        // Return rendered view
        $view = new SiteBuilderTemplate;
        $view->__setVars($data);
        return $view->render($this->config['template_path'].$data['template']);
    }

}

