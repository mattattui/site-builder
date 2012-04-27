<?php
namespace Inanimatt\SiteBuilder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Inanimatt\SiteBuilder\ContentCollection\ContentCollectionInterface;
use Inanimatt\SiteBuilder\ContentHandler\ContentHandlerInterface;
use Inanimatt\SiteBuilder\Serialiser\SerialiserInterface;
use Inanimatt\SiteBuilder\Renderer\RendererInterface;


class SiteBuilder
{
    protected $default_template = 'template.php';
    protected $template_path = './';
    
    protected $renderers = null;
    
    public function __construct()
    {
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
    public function renderSite(ContentCollectionInterface $contentCollection, SerialiserInterface $serialiser)
    {
        $contentObjects = $contentCollection->getObjects();
        foreach($contentObjects as $content) {
            $output = $this->renderFile($content, array(
                'app' => array(
                    'contentcollection' => $contentCollection,
                    'contentobject' => $content,
                ),
            ));
            $serialiser->write($output, $content->getOutputName($serialiser->getOutputExtension()));
        }
    }
    
    /**
     * Render given content object with chosen (or default) template, and return output
     * 
     * @param ContentHandlerInterface $file ContentHandler object
     * @return string Rendered content
     */
    public function renderFile(ContentHandlerInterface $file, $extraData = null)
    {
        // Handle content and data first
        $data = $file->getMetadata();
        $content = $file->getContent();
        
        if (!is_null($extraData)) {
            if (!is_array($extraData)) {
                throw new SiteBuilderException('Invalid argument: extra data must be an array.');
            }
            $data = array_merge($extraData, $data);
        }
        
        // Choose renderer
        if (!isset($data['template']) || !$data['template']) {
            $data['template'] = $this->default_template;
        }
        
        $pos = strrpos($data['template'],'.');
        if ($pos === false) {
            throw new SiteBuilderException('Cannot determine renderer for template '.$data['template']);
        }
        
        $rendererId = substr($data['template'],$pos+1);
        
        if (!isset($this->renderers[$rendererId])) {
            throw new SiteBuilderException('No renderer registered for template file extension .'.$rendererId);
        }
            
        return $this->renderers[$rendererId]->render($data, $data['template']);
    }

}

