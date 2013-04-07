<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Inanimatt\SiteBuilder\FrontmatterReader;
use \Twig_Environment;

class TwigHtmlTransformer implements TransformerInterface
{
    protected $frontMatterReader;
    protected $twig;
    protected $template;

    public function __construct(FrontmatterReader $reader, Twig_Environment $twig, $template)
    {
        $this->frontMatterReader = $reader;
        $this->twig = $twig;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return array('htm', 'html');
    }

    /**
     * {@inheritdoc}
     */
    public function transform($originFile, $targetFile)
    {
        $fileContent = file_get_contents($originFile);

        list($fileContent, $data) = $this->frontMatterReader->parse($fileContent);
        $data['content'] = $fileContent;

        // Override template?
        if (isset($data['template'])) {
            $this->template = $data['template'];
        }

        // Render and save
        $output = $this->twig->render($this->template, $data);

        file_put_contents($targetFile, $output);
    }
}