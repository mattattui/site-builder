<?php

namespace Inanimatt\SiteBuilder\Serialiser;

/**
 * Serialiser interface.
 *
 * Serialisers are expected to write rendered content to an output device
 * like a local disk, a remote file share, or web service.
 */
interface SerialiserInterface
{

    /**
     * Write content that corresponds to the given relative URL.
     *
     * @param string $content Rendered HTML content
     * @param string $path    Expected URL
     *
     * @throws Inanimatt\SiteBuilder\Exception\SerialiserException
     * @return null
     */
    public function write($content, $path);

}
