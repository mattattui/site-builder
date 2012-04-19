<?php
namespace Inanimatt\SiteBuilder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Inanimatt\SiteBuilder\ContentCollection\ContentCollectionInterface;
use Inanimatt\SiteBuilder\ContentObject\ContentObjectInterface;
use Inanimatt\SiteBuilder\Serialiser\SerialiserInterface;


class SiteBuilder
{
    protected $twig = null;
    protected $contentCollection = null;
    protected $serialiser = null;
    
    protected $default_template = 'template.php';
    protected $template_path = './';
    
    public function __construct(\Twig_Environment $twig, ContentCollectionInterface $contentCollection, SerialiserInterface $serialiser)
    {
        $this->twig = $twig;
        $this->contentCollection = $contentCollection;
        $this->serialiser = $serialiser;
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
    
    
    public function renderFile(ContentObjectInterface $file)
    {
        // Handle content and data first
        $data = $file->getMetadata();
        $content = $file->getContent();
        
        if (!isset($data['template']) || !$data['template']) {
            $data['template'] = $this->default_template;
        }
        
        // Insert content into template
        // FIXME: replace the twig dependency with a RendererFactory with handlers
        $templateInfo = new \splFileInfo($data['template']);
        switch($templateInfo->getExtension()) {
            case 'twig':
                $renderer = new Renderer\TwigRenderer($this->twig);
                break;
            case 'php':
                $renderer = new Renderer\PhpRenderer($this->template_path);
                break;
            default:
                throw new SiteBuilderException('Unsupported template type');
        }

        // Return rendered view
        return $renderer->render($data, $data['template']);
    }

}

