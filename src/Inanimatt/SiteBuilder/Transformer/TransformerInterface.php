<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;

interface TransformerInterface
{
    /**
     * Transform a file
     */
    public function transform(FileCopyEvent $event);
}
