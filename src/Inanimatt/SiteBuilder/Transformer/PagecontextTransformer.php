<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use \Twig_Environment;

class PagecontextTransformer implements TransformerInterface
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
        $path_parts = pathinfo($relative_path);
        $page_name = $path_parts['filename'];
        $path = explode(DIRECTORY_SEPARATOR, $relative_path, -1);
        
        $event->data->set('siteroot', $site_root);
        $event->data->set('breadcrumbs', $path);
        $event->data->set('pagename', $page_name);
    }
}
