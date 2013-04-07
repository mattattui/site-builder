<?php

namespace Inanimatt\SiteBuilder;

use Symfony\Component\Filesystem\Filesystem;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;

class TransformingFilesystem extends Filesystem
{
    protected $transformers;

    public function __construct()
    {
        $this->transformers = array();
    }

    public function registerTransformer(TransformerInterface $transformer)
    {
        foreach ($transformer->getSupportedExtensions() as $extension) {
            $this->contentTransformers[$extension] = $transformer;
        }
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
        $this->mkdir(dirname($targetFile));

        if (!$override && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        } else {
            $doCopy = true;
        }

        if ($doCopy) {
            // Transform $originFile if transformer exists
            $extension = pathinfo($originFile, PATHINFO_EXTENSION);
            if (isset($this->contentTransformers[$extension])) {
                $originFile = $this->contentTransformers[$extension]->transform($originFile, $targetFile);
            } elseif (true !== @copy($originFile, $targetFile)) {
                echo 'Not transforming '.$originFile.PHP_EOL;
                throw new IOException(sprintf('Failed to copy %s to %s', $originFile, $targetFile));
            }
        }
    }
}
