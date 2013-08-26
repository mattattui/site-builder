<?php

namespace Inanimatt\SiteBuilder;

use Inanimatt\SiteBuilder\FilesystemEvents;
use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class TransformingFilesystem extends Filesystem
{
    protected $event_dispatcher;

    public function __construct(EventDispatcher $event_dispatcher)
    {
        $this->event_dispatcher = $event_dispatcher;
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
        if (stream_is_local($originFile) && !is_file($originFile)) {
            throw new IOException(sprintf('Failed to copy %s because file does not exist', $originFile));
        }

        $this->mkdir(dirname($targetFile));

        if (!$override && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        } else {
            $doCopy = true;
        }

        if (!$doCopy) {
            return;
        }

        $event = new FileCopyEvent($originFile, $targetFile);
        $event = $this->event_dispatcher->dispatch(FilesystemEvents::COPY, $event);

        $originFile = $event->getSource();
        $targetFile = $event->getTarget();

        if ($event->isModified()) {
            file_put_contents($targetFile, $event->getContent());
            return;
        }

        // No listeners modified the file, so just copy it (original behaviour & code)
        // https://bugs.php.net/bug.php?id=64634
        $source = fopen($originFile, 'r');
        $target = fopen($targetFile, 'w+');
        stream_copy_to_stream($source, $target);
        fclose($source);
        fclose($target);
        unset($source, $target);

        if (!is_file($targetFile)) {
            throw new IOException(sprintf('Failed to copy %s to %s', $originFile, $targetFile));
        }
    }
}
