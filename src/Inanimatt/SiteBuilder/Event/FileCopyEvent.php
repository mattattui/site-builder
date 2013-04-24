<?php

namespace Inanimatt\SiteBuilder\Event;

use PhpCollection\Map;
use Symfony\Component\EventDispatcher\Event;

class FileCopyEvent extends Event
{
    /**
     * Source filename
     *
     * @var string
     */
    protected $source;

    /**
     * Target filename
     *
     * @var string
     */
    protected $target;

    /**
     * File contents
     *
     * @var string
     */
    protected $content = null;

    /**
     * Whether the file has been modified
     *
     * @var boolean
     */
    protected $is_modified = false;

    /**
     * File metadata
     */
    public $data;

    public function __construct($source, $target)
    {
        $this->source = $source;
        $this->target = $target;
        $this->extension = pathinfo($source, PATHINFO_EXTENSION);
        $this->data = new Map();
    }

    public function getSource ()
    {
        return $this->source;
    }

    public function setTarget ($target)
    {
        $this->target = $target;
    }

    public function getTarget ()
    {
        return $this->target;
    }

    public function getExtension ()
    {
        return $this->extension;
    }

    public function isModified ()
    {
        return $this->is_modified;
    }

    public function setIsModified ($is_modified)
    {
        $this->is_modified = $is_modified;
    }

    public function getContent ()
    {
        if (is_null($this->content)) {
            $this->content = file_get_contents($this->source);
        }

        return $this->content;
    }

    public function setContent ($content)
    {
        $this->is_modified = true;
        $this->content = $content;
    }
}
