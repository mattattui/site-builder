<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use \Twig_Environment;

class TwigHtmlTransformer implements TransformerInterface
{
    protected $twig;
    protected $template;

    public function __construct(Twig_Environment $twig, $template)
    {
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

        $data = iterator_to_array($event->data);
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
