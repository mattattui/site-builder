<?php

namespace Inanimatt\SiteBuilder\Transformer;

interface TransformerInterface
{
    /**
     * Return an array of supported file extensions
     *
     * @return array File extensions
     */
    public function getSupportedExtensions();

    /**
     * Copy and transform a file
     */
    public function transform($originFile, $targetFile);
}
