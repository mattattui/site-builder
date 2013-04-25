<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Inanimatt\SiteBuilder\FrontmatterReader;

class FrontmatterTransformer implements TransformerInterface
{
    protected $frontmatterReader;

    public function __construct(FrontmatterReader $reader)
    {
        $this->frontmatterReader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FileCopyEvent $event)
    {
        if (!in_array($event->getExtension(), array('md', 'markdown', 'html', 'htm'))) {
            return;
        }

        $fileContent = $event->getContent();

        list($fileContent, $data) = $this->frontmatterReader->parse($fileContent);

        $event->setContent($fileContent);

        foreach ($data as $key => $value) {
            $event->data->set($key, $value);
        }
    }
}
