<?php
namespace Inanimatt\SiteBuilder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Inanimatt\SiteBuilder\ContentCollection\ContentCollectionInterface;
use Inanimatt\SiteBuilder\ContentHandler\ContentHandlerInterface;
use Inanimatt\SiteBuilder\Serialiser\SerialiserInterface;
use Inanimatt\SiteBuilder\Renderer\RendererInterface;


class SiteBuilder
{
    protected $contentCollection = null;
    protected $serialiser = null;
    
    protected $default_template = 'template.php';
    protected $template_path = './';
    
    protected $renderers = null;
    
    public function __construct(ContentCollectionInterface $contentCollection, SerialiserInterface $serialiser)
    {
        $this->contentCollection = $contentCollection;
        $this->serialiser = $serialiser;
        $this->renderers = array();
    }
    
    
    public function registerRenderer(RendererInterface $renderer, $extensions) {
        if (!is_array($extensions)) {
            throw new SiteBuilderException('Invalid argument: 2nd argument must be an array.');
        }
        
        foreach($extensions as $ext) {
            $this->renderers[$ext] = $renderer;
        }
    }
    
    public function setDefaultTemplate($template)
    {
        $this->default_template = $template;
    }

    public function setTemplatePath($path)
    {
        $this->template_path = $path;
    }
        
    
    // Get a list of content objects, run the renderer on each one, then serialise them
    public function renderSite()
    {
        $contentObjects = $this->contentCollection->getObjects();
        foreach($contentObjects as $content) {
            $output = $this->renderFile($content);
            $this->serialiser->write($output, $content->getName());
        }
    }
    
    
    public function renderFile(ContentHandlerInterface $file)
    {
        // Handle content and data first
        $data = $file->getMetadata();
        $content = $file->getContent();
        
        if (!isset($data['template']) || !$data['template']) {
            $data['template'] = $this->default_template;
        }
        
        // Insert content into template
        // FIXME: templates can only ever be files while splFileInfo used to determine type
        $templateInfo = new \splFileInfo($data['template']);
        
        if (!isset($this->renderers[$templateInfo->getExtension()])) {
            throw new SiteBuilderException('No renderer registered for template file extension .'.$templateInfo->getExtension());
        }
            
        return $this->renderers[$templateInfo->getExtension()]->render($data, $data['template']);
    }

}

