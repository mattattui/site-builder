<?php

namespace Inanimatt\SiteBuilder\Transformer;

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
    public function getSupportedExtensions()
    {
        return array('sass', 'scss');
    }

    /**
     * {@inheritdoc}
     */
    public function transform($originFile, $targetFile)
    {
        if (!$this->sass_bin) {
            copy($originFile, $targetFile);
            return;
        }

        if (!is_executable($this->sass_bin)) {
            throw new \RuntimeException('Configured sass binary not found or not executable.');
        }

        $type = '';
        if (pathinfo($originFile, PATHINFO_EXTENSION) === 'scss') {
            $type = '--scss';
        }

        $targetFile = substr($targetFile, 0, 0 - strlen(pathinfo($targetFile, PATHINFO_EXTENSION))) . 'css';

        $builder = new ProcessBuilder(array($this->sass_bin, $type, '--no-cache', $originFile, $targetFile));
        $builder->getProcess()->run();
    }
}
