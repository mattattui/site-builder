<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use \Twig_Environment;

class SiterootTransformer implements TransformerInterface
{
    protected $output_path;

    public function __construct($output_path)
    {
        if (substr($output_path, -1) !== DIRECTORY_SEPARATOR) {
            $output_path .= DIRECTORY_SEPARATOR;
        }
        $this->output_path = $output_path;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FileCopyEvent $event)
    {
        $relative_path = str_replace($this->output_path, '', $event->getTarget());
        $site_root = str_repeat('..'.DIRECTORY_SEPARATOR, substr_count($relative_path, DIRECTORY_SEPARATOR));

        $event->data->set('siteroot', $site_root);
    }
}
