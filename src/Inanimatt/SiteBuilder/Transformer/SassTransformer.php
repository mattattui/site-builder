<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Symfony\Component\Process\ProcessBuilder;

class SassTransformer implements TransformerInterface
{
    protected $sass_bin;

    public function __construct($sass_bin, $style = 'compressed')
    {
        $this->sass_bin = $sass_bin;

        if (!in_array($style, array('compressed', 'compact', 'nested', 'expanded'))) {
            throw new \InvalidArgumentException(
                'Style argument must be one of "compressed", "compact", "nested", or "expanded".'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FileCopyEvent $event)
    {
        if (!in_array($event->getExtension(), array('sass', 'scss'))) {
            return;
        }

        if (!$this->sass_bin) {
            return;
        }

        if (!is_executable($this->sass_bin)) {
            throw new \RuntimeException('Sass file found, but sass compiler either not installed or not configured.');
        }

        $type = '';
        if ($event->getExtension() === 'scss') {
            $type = '--scss';
        }

        $targetFile = $event->getTarget();
        $targetFile = substr($targetFile, 0, 0 - strlen(pathinfo($targetFile, PATHINFO_EXTENSION))) . 'css';
        $event->setTarget($targetFile);

        $builder = new ProcessBuilder(array($this->sass_bin, $type, '--no-cache', $event->getSource()));
        $process = $builder->getProcess();
        $process->run();

        $event->setContent($process->getOutput());
    }
}
