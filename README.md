[![Build Status](https://secure.travis-ci.org/inanimatt/site-builder.png?branch=master)](http://travis-ci.org/inanimatt/site-builder)

# Site-builder

Site-builder is a simple static site generator. It allows you to create and
manage a website out of simple text files and templates. This gives you many of
the advantages of a CMS, but because the result is plain old HTML, it's more
secure and has higher performance.

Site-builder works by copying files from your content directory to an output
directory, applying one or more transformation filters on the way.

**Note:** There are more stable and better-supported static site generators out there; this is just a personal project to help me learn and improve my code. If you're looking for a well-supported and very capable static site generator, look at [Jekyll](http://jekyllrb.com/) (Ruby), [Hyde](http://ringce.com/hyde) (Python), or [Phrozn](http://www.phrozn.info/en/) or [PieCrust](http://bolt80.com/piecrust/) (both PHP).

## Quick Start

1. [Download the .phar file](https://lazycat.org/download/sitebuilder.phar). 
   It's the whole app in one file, with everything it needs to run.
2. Put `sitebuilder.phar` in the directory where you want to keep the installation.
3. Run `php sitebuilder.phar init` to create directories, config, and sample files.
4. Test your installation with `php sitebuilder.phar rebuild`. If it works, you 
   should see a generated `example.html` file in the `output` directory.
5. Replace the default template with your own. Twig is a pretty straightforward 
   template language; just put {{ content | raw }} where you want your page 
   content to appear. Read on for more help and links to the Twig docs.
6. Create your content files. You can write plain HTML (and save as `.html`),
   or Markdown (and save as `.md` or `.markdown`). You can make sub-directories
   too. Read on for more info on Markdown, including a link to the
   documentation.
7. Run `php sitebuilder.phar rebuild` to regenerate your site.

## Why use it?

Using a CMS or Apache/PHP includes will build your site dynamically upon
request, which adds a lot of overhead - your content probably doesn't change
very often, and all you really need those things for is to keep your content
and your templates separate.

By contrast, static site generators run offline and rebuild your site as flat
HTML when you change your content. A web server like Apache can deliver flat
HTML files hundreds of times more efficiently than processing PHP files every
time they're requested.

Site-builder is yet another SSG. Out of the box it supports Twig templates, and
content files in HTML and Markdown. It's also extensible, so you can add
transformations for any other behaviour you want.

## Requirements

* PHP 5.3 or newer. If you use Ubuntu or Debian, you may need to install 
  the `php5-cli` package to let you run scripts on the command-line.

If you aren't running the .phar edition, you'll need these:

* Twig 1.6 or newer
* "dflydev"'s Markdown library
* Symfony2 Components: Yaml, Config, DependencyInjection, ClassLoader, Console

A `composer.json` file is included to handle the installation of these 
requirements. See the next section for more about this.



## Installation

1. [Download](https://github.com/inanimatt/site-builder/zipball/master) or clone this repository.
2. From the command-line, run `curl http://getcomposer.org/installer | php` and follow the on-screen instructions to install Composer.
3. Run `php composer.phar install` to install all the required libraries into the `vendor` directory.
4. Test the installation by running `php sitebuilder.php rebuild`. Check for files in the `output` directory.
5. If you're helping to develop Site-builder, run `php composer.phar install --dev` to install PHPUnit, and run it with `vendor/bin/phpunit`. You can copy `phpunit.xml.dist` to `phpunit.xml` if you want to change it. Rebuild the phar with the `compile.php` script.

## Usage

### The basics

1. Put your content (e.g. `index.html`, `about-me.md`) into the `content`
directory. Content files are just that: the main content of the page you want
to publish. The name of the file when you publish will be the same as the
content file, except with `.html` instead of the original extension. You can
create sub-directories in your content directory and they'll be created in the
output directory when you publish, so don't feel like you have to cram
everything into the same directory.

2. The default template is `template.twig` in the `templates` directory. Change
it however you like. You can also change the default template in `config.ini`.
More on that later. The content of your content files is placed into the
template variable called `$content` (or `{{content}}` in Twig).

3. Run `php sitebuilder.php rebuild` to render every file and save it to the
`output` directory.


### Templates

Twig is a fast, clean, and extensible template language with a syntax very 
similar to Jinja and Django's templating systems. Read more about [writing Twig 
templates](http://twig.sensiolabs.org/doc/templates.html) here. 

Twig escapes output by default, which is very safe and good practice. However
the `content` variable which contains your page content probably shouldn't be
escaped. You can tell Twig not to escape it by passing it through the `raw`
filter, i.e. `{{ content | raw }}`


### Content

Site-builder accepts content in either HTML or Markdown format. Pick whichever
you prefer, or use both. Both have an optional "front matter" block which contains instructions you can pass to the template.


### HTML & Twig content format

Look at `content/example.html` for an example. The file is like any other HTML
file and you can write whatever you like into it. The only difference is an
optional front matter block, which allows you to pass more information to the
template when you rebuild the site.

When the page is published, the contents of the file are passed to the template
in the `content` variable along with anything else you define in your front
matter block.


```html
---
title: This is my page title
---

<h2>Hello world!</h2>
<p>This is my content file. There are many like it but this one is mine.</p>
```

If you set the `template` variable in your front matter, then Site-builder will
render the page with that template instead of the default.


### Markdown & Twig content format

Markdown is an "easy-to-read, easy-to-write plain text format", which is then
turned into valid, clean HTML. It was developed by John Gruber and is very
popular. [This page](https://raw.github.com/inanimatt/site-builder/master/README.md) itself
was written in Markdown. A simple example markdown content file can be found in
`content/markdown-example.md`.

Read more about [writing 
Markdown](http://daringfireball.net/projects/markdown/basics) here.

There are four important things to note about Markdown content files in 
Site-builder:

1. Markdown can contain plain old HTML, so don't feel constrained by it!
2. Your markdown content files should end in either `.markdown` or `.md`
3. Site-builder looks for (but doesn't require) a "front-matter" block where 
   you can set variables to be passed to your template.
4. Site-builder uses [Markdown Extra](http://michelf.ca/projects/php-markdown/extra/) by default. It add 
   some features to Markdown, like tables, ids, code blocks, and footnotes.

The front-matter block is written in YAML and looks like this:

```yaml
---
title: This is my page title
template: myTemplate.twig
---

My page title
=============

Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor 
incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis 
nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 

Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu 
fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
culpa qui officia deserunt mollit anim id est laborum.

```

When you rebuild the site, Markdown files are converted into HTML and then 
passed to either the default template, or whichever template you named in the 
front-matter block. The HTML content is set on the `{{ content | raw }}` variable in the template.



## Contributing

I'd love to have pull requests to improve Site-Builder. Please raise an issue 
first though, in case someone's already working on the feature.

* I think I've got good unit test coverage, but I'm no expert. Any help with tests would be appreciated.
  
* Documentation (end user and developer). I've started to add doc-comments
  and there's this README, but there could be more and better, I know.

* A navigation generator object passed to the templates that represents the
  site structure, so that templates can create left navigation. It should
  ignore resource files and be context aware (so links in sub-directories don't
  break).

## Contributors

* [inouire](https://github.com/inouire)
