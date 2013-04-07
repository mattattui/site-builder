<?php

namespace Inanimatt\SiteBuilder;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TransformingFilesystem extends Filesystem implements LoggerAwareInterface
{
    protected $contentTransformers;
    protected $logger;

    public function __construct()
    {
        $this->contentTransformers = array();
        $this->logger = new NullLogger;
    }

    public function addTransformer(TransformerInterface $transformer)
    {
        foreach ($transformer->getSupportedExtensions() as $extension) {
            $this->contentTransformers[$extension] = $transformer;
        }
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Copies and transforms a file.
     *
     * This method only copies the file if the origin file is newer than the target file.
     *
     * By default, if the target already exists, it is not overridden.
     *
     * @param string  $originFile The original filename
     * @param string  $targetFile The target filename
     * @param boolean $override   Whether to override an existing file or not
     *
     * @throws IOException When copy fails
     */
    public function copy($originFile, $targetFile, $override = false)
    {
        $this->logger->debug('Copying {source} to {target}', array('source' => $originFile, 'target' => $targetFile));
        $this->mkdir(dirname($targetFile));

        if (!$override && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        } else {
            $doCopy = true;
        }

        $this->logger->debug('doCopy is {doCopy}', array('doCopy' => $doCopy ? 'true' : 'false'));

        if ($doCopy) {
            // Transform $originFile if transformer exists
            $extension = pathinfo($originFile, PATHINFO_EXTENSION);
            if (isset($this->contentTransformers[$extension])) {
                $this->logger->debug('Using transformer for {extension}', array('extension' => $extension));
                $originFile = $this->contentTransformers[$extension]->transform($originFile, $targetFile);
            } elseif (true !== @copy($originFile, $targetFile)) {
                throw new IOException(sprintf('Failed to copy %s to %s', $originFile, $targetFile));
            }
        }
    }
}
