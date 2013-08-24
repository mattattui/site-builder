<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use dflydev\markdown\MarkdownParser;
use \Twig_Environment;

class TwigMarkdownTransformer implements TransformerInterface
{
    protected $markdown;
    protected $twig;
    protected $template;

    public function __construct(MarkdownParser $markdown, Twig_Environment $twig, $template)
    {
        $this->markdown = $markdown;
        $this->twig = $twig;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FileCopyEvent $event)
    {
        if (!in_array($event->getExtension(), array('md', 'markdown'))) {
            return;
        }

        $fileContent = $event->getContent();

        // Override template?
        $template = $event->data->get('template')->getOrElse($this->template);

        /* Parse remaining file as markdown */
        $data = iterator_to_array($event->data);
        $data['content'] = $this->markdown->transformMarkdown($fileContent);

        // Render and save
        $output = $this->twig->render($template, $data);

        $targetFile = $event->getTarget();
        $targetFile = substr($targetFile, 0, 0 - strlen(pathinfo($targetFile, PATHINFO_EXTENSION))) . 'html';

        $event->setTarget($targetFile);
        $event->setContent($output);
    }
}
