<?php

namespace Inanimatt\SiteBuilder\Transformer;

use Inanimatt\SiteBuilder\Transformer\TransformerInterface;
use dflydev\markdown\MarkdownParser;
use \Twig_Environment;
use Symfony\Component\Yaml\Parser as YamlParser;

class TwigMarkdownTransformer implements TransformerInterface
{
    protected $markdown;
    protected $yaml;
    protected $twig;
    protected $template;

    public function __construct(MarkdownParser $markdown, YamlParser $yaml, Twig_Environment $twig, $template)
    {
        $this->markdown = $markdown;
        $this->yaml = $yaml;
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

        $data = array();
        $frontMatter = $this->getFrontMatter($fileContent);
        $data = $this->yaml->parse($frontMatter);

        /* Strip (from the start of the string) the length of the front-matter
         * collected, plus the size of the delimiters, and the starting and
         * ending line-breaks
         * FIXME: This assumes CRLF (or whatever PHP_EOL turns out to be), which
         * is bad.
         */
        $fileContent = substr($fileContent, mb_strlen($frontMatter, 'UTF-8') + 6 + (2 * strlen(PHP_EOL)));

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

    protected function getFrontMatter($content)
    {
        $content = ltrim($content);

        if ((substr($content,0,3) === '---') && preg_match('/^\-\-\-/m', $content, $matches, PREG_OFFSET_CAPTURE, 3)) {
            $end = $matches[0][1];

            return substr($content,3,$end-3);
        }

    }
}