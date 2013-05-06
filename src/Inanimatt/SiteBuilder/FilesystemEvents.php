<?php

namespace Inanimatt\SiteBuilder;

final class FilesystemEvents
{
    /**
     * The filesystem.copy event is thrown before a file is copied by the TransformingFilesystem class
     *
     * The event listener receives a FileCopyEvent instance
     *
     * @var string
     */
    const COPY = 'filesystem.copy';
}
