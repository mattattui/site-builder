<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Symfony\Component\Process\ProcessBuilder;

class SassTransformer implements TransformerInterface
{
    protected $sass_bin;

    public function __construct($process_builder)
    {
        $this->sass_process_builder = $process_builder;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FileCopyEvent $event)
    {
        if (!in_array($event->getExtension(), array('sass', 'scss'))) {
            return;
        }

        if (!$this->sass_process_builder->isInstalled()) {
            return;
        }

        $targetFile = $event->getTarget();
        $targetFile = substr($targetFile, 0, 0 - strlen(pathinfo($targetFile, PATHINFO_EXTENSION))) . 'css';
        $event->setTarget($targetFile);

        $process = $this->sass_process_builder->getProcess($event->getSource());
        $process->run();

        $event->setContent($process->getOutput());
    }
}
