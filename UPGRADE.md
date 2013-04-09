# Upgrading to SiteBuilder 3.x

## Notes for users

* The default behaviour is now to pass unrecognised files straight through. This means you can put your whole site in the `content` folder and publish it to the `output folder`, php files, images and all.
* The default Markdown parser is now the MarkdownExtra parser.
* Otherwise, content files that use Markdown with Twig templates should behave exactly as before.
* There is a new Twig & HTML content format. It uses the same frontmatter format as the Twig & Markdown format, but doesn't process the remaining content. It reads `.html` files.
* The Sitebuilder template format has been removed in favour of the Twig & HTML content format. 
* The `rebuild` command has a new `--force` option, which always reprocesses files even if they're not newer
* The `rebuild` command has a new `--delete` option, which removes any files in `output` that don't exist in `content`.

## Developer information

Um… everything changed. Everything. I mean, the name's the same, but…

The new architecture looks like this:

* Sitebuilder's main class is now the `TransformingFilesystem` class. It extends the [Symfony Filesystem component](http://symfony.com/doc/current/components/filesystem.html), and acts the same way except for one important detail: it dispatches a FileCopy event for each file, which you can hook into in order to transform or modify files or file names. 
* The Sitebuilder `rebuild` console command now creates a TransformingFilesystem object and uses the `mirror` method to copy everything from `content` to `output`, transforming files according to its registered listeners.
* You can register new transformers in the `src/services.yml` file by tagging your new class with `sitebuilder.transformer` (in which case they'll be added automatically to the event dispatcher).
* Transformers receive a FileCopyEvent and can get/set the content, modify the target file name, etc.
