<?php

namespace Inanimatt\SiteBuilder;

use Symfony\Component\Yaml\Parser as YamlParser;

class FrontmatterReader
{
    public function __construct(YamlParser $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * Find and parse a frontmatter block
     * 
     * @return array 0: the content (with frontmatter removed if found), 1: any parsed frontmatter data
     */
    public function parse($content)
    {
        $frontmatter = '';
        $data = array();
        
        if ((substr($content,0,3) === '---') && preg_match('/^\-\-\-/m', $content, $matches, PREG_OFFSET_CAPTURE, 3)) {
            $end = $matches[0][1];

            $frontMatter = substr($content,3,$end-3);
            $data = $this->yaml->parse($frontMatter);
            $content = ltrim(substr($content, 3 + $matches[0][1]), "\r\n");
        }

        return array($content, $data);
    }

}