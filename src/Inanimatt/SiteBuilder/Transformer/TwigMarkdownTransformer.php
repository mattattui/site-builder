<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use Inanimatt\SiteBuilder\FrontmatterReader;
use dflydev\markdown\MarkdownParser;
use \Twig_Environment;

class TwigMarkdownTransformer implements TransformerInterface
{
    protected $markdown;
    protected $frontmatterReader;
    protected $twig;
    protected $template;

    public function __construct(MarkdownParser $markdown, FrontmatterReader $reader, Twig_Environment $twig, $template)
    {
        $this->markdown = $markdown;
        $this->frontmatterReader = $reader;
        $this->twig = $twig;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return array('md', 'markdown');
    }

    /**
     * {@inheritdoc}
     */
    public function transform($originFile, $targetFile)
    {
        $fileContent = file_get_contents($originFile);

        list($fileContent, $data) = $this->frontmatterReader->parse($fileContent);

        // Override template?
        if (isset($data['template'])) {
            $this->template = $data['template'];
        }

        /* Parse remaining file as markdown */
        $data['content'] = $this->markdown->transformMarkdown($fileContent);

        // Render and save
        $output = $this->twig->render($this->template, $data);

        $targetFile = substr($targetFile, 0, 0 - strlen(pathinfo($targetFile, PATHINFO_EXTENSION))) . 'html';

        file_put_contents($targetFile, $output);
    }
}
