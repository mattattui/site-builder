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
    
    
    /**
     * Register a renderer to handle the given file extension(s)
     * 
     * @param RendererInterface $renderer An object that implements the RendererInterface
     * @param array $extensions One or more file extensions without the initial .
     */
    public function registerRenderer(RendererInterface $renderer, $extensions) {
        if (!is_array($extensions)) {
            throw new SiteBuilderException('Invalid argument: 2nd argument must be an array.');
        }
        
        foreach($extensions as $ext) {
            $this->renderers[$ext] = $renderer;
        }
    }
    
    /**
     * Set default template filename
     * 
     * @param string $template Template filename (without path)
     */
    public function setDefaultTemplate($template)
    {
        $this->default_template = $template;
    }

    /**
     * Set template search path
     * 
     * @param string $path Path to templates
     */
    public function setTemplatePath($path)
    {
        $this->template_path = $path;
    }
        
    
    /**
     * Iterate through the content collection, render each file, serialise output
     */
    public function renderSite()
    {
        $contentObjects = $this->contentCollection->getObjects();
        foreach($contentObjects as $content) {
            $output = $this->renderFile($content);
            $this->serialiser->write($output, $content->getName());
        }
    }
    
    /**
     * Render given content object with chosen (or default) template, and return output
     * 
     * @param ContentHandlerInterface $file ContentHandler object
     * @return string Rendered content
     */
    public function renderFile(ContentHandlerInterface $file)
    {
        // Handle content and data first
        $data = $file->getMetadata();
        $content = $file->getContent();
        
        if (!isset($data['template']) || !$data['template']) {
            $data['template'] = $this->default_template;
        }
        
        // Insert content into template
        // FIXME: templates can only ever be files while splFileInfo used to determine type. 
        // Create template handler object, like content handler? 
        $templateInfo = new \splFileInfo($data['template']);
        
        if (!isset($this->renderers[$templateInfo->getExtension()])) {
            throw new SiteBuilderException('No renderer registered for template file extension .'.$templateInfo->getExtension());
        }
            
        return $this->renderers[$templateInfo->getExtension()]->render($data, $data['template']);
    }

}

