<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Symfony\Component\Process\ProcessBuilder;

class SassProcessBuilder
{
    protected $sass_bin;
    
    protected $style;

    public function __construct($sass_bin, $style = 'compressed')
    {
        $this->sass_bin = $sass_bin;
        $this->style = $style;

        if (!is_executable($sass_bin)) {
            throw new \InvalidArgumentException(
                'Sass compiler not installed, or not configured. Check your config.ini'
            );
        }

        if (!in_array($style, array('compressed', 'compact', 'nested', 'expanded'))) {
            throw new \InvalidArgumentException(
                'Style argument must be one of "compressed", "compact", "nested", or "expanded".'
            );
        }
    }

    public function isInstalled()
    {
        return file_exists($this->sass_bin) && is_executable($this->sass_bin);
    }

    public function getProcess($filename)
    {
        $type = '';
        if (pathinfo($filename, PATHINFO_EXTENSION) === 'scss') {
            $type = '--scss';
        }
        
        $builder = new ProcessBuilder(array($this->sass_bin, $type, '--no-cache', '--style='.$this->style, $filename));
        return $builder->getProcess();
    }
}