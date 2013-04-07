# Upgrading to SiteBuilder 3.x

## Notes for users

* Twig & Markdown content files should behave exactly as before
* There is a new Twig & HTML content format. It uses the same frontmatter format as the Twig & Markdown format, but doesn't process the remaining content.
* The Sitebuilder template format has been removed in favour of the Twig & HTML content format. 
* The default behaviour is now to pass unrecognised files straight through. This means you can put your whole site in the `content` folder and publish it to the `output folder`, images and all.
* The `rebuild` command has a new `--force` option, which always reprocesses files even if they're not newer
* The `rebuild` command has a new `--delete` option, which removes any files in `output` that don't exist in `content`.

## Developer information

Um… everything changed. Everything. I mean, the name's the same, but…

The new architecture looks like this:

* Sitebuilder's main class is now the `TransformingFilesystem` class. It extends the [Symfony Filesystem component](http://symfony.com/doc/current/components/filesystem.html), and acts the same way except for one important detail: you can register handlers to transform content based on the file extension when a file is copied.
* The Sitebuilder `rebuild` console command now creates a TransformingFilesystem object and uses the `mirror` method to copy everything from `content` to `output`, transforming files according to its registered transformers.
* You can register new transformers in the `src/services.yml` file, either by adding a `calls` instruction to the `sitebuilder_filesystem` definition, or by tagging your new class with `sitebuilder.transformer` (in which case they'll be added automatically).
* Transformers are responsible for reading an input file from the content directory and producing a corresponding file in the output director. What they do and how they do that is up to you.
