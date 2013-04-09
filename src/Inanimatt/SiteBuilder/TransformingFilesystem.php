<?php

namespace Inanimatt\SiteBuilder;

use Inanimatt\SiteBuilder\FilesystemEvents;
use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class TransformingFilesystem extends Filesystem implements LoggerAwareInterface
{
    protected $logger;
    protected $event_dispatcher;

    public function __construct(EventDispatcher $event_dispatcher)
    {
        $this->contentTransformers = array();
        $this->logger = new NullLogger;
        $this->event_dispatcher = $event_dispatcher;
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
        $this->mkdir(dirname($targetFile));

        if (!$override && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        } else {
            $doCopy = true;
        }

        if ($doCopy) {

            $event = new FileCopyEvent($originFile, $targetFile);
            $event = $this->event_dispatcher->dispatch(FilesystemEvents::COPY, $event);

            $originFile = $event->getSource();
            $targetFile = $event->getTarget();

            if ($event->isModified()) {
                // FIXME: HMMM, is this right? What if we need to generate more than one file?
                file_put_contents($targetFile, $event->getContent());
            } else {
                // No listeners modified the file, so just copy it.
                if (true !== @copy($originFile, $targetFile)) {
                    throw new IOException(sprintf('Failed to copy %s to %s', $originFile, $targetFile));
                }
            }
        }
    }
}
