<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Inanimatt\SiteBuilder\FrontmatterReader;
use \Twig_Environment;

class TwigHtmlTransformer implements TransformerInterface
{
    protected $frontMatterReader;
    protected $twig;
    protected $template;

    public function __construct(FrontmatterReader $reader, Twig_Environment $twig, $template)
    {
        $this->frontMatterReader = $reader;
        $this->twig = $twig;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FileCopyEvent $event)
    {
        if (!in_array($event->getExtension(), array('htm', 'html'))) {
            return;
        }

        $fileContent = $event->getContent();

        list($fileContent, $data) = $this->frontMatterReader->parse($fileContent);
        $data['content'] = $fileContent;

        // Override template?
        if (isset($data['template'])) {
            $this->template = $data['template'];
        }

        // Render and save
        $output = $this->twig->render($this->template, $data);

        $event->setContent($output);
    }
}
