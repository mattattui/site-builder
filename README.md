What is(n't) this?
==================

Site-builder is a simple example of a static site generator. Static site
generators take a series of content files, wrap them in templates, then 
save them as HTML files. This is an alternative to the common practice of
using PHP to `include()` headers and footers to content files "live" 
whenever the file is requested. 

Using an SSG gives similar advantages (your content and layout are separated), 
dramatically increases the performance of your website over using PHP for the 
same task, and reduces requirements for your website.

Site-builder is yet another SSG. Out of the box it supports Twig and its own 
simple variety of PHP templates, and content files in HTML, PHP, and Markdown.
It also presents a number of interfaces to allow developers to create 
additional renderers and content handlers.

**Note:** Site-builder is very much a work in progress. Use it at your own 
risk, and don't be surprised if new updates break backward compatibility. It's 
essentially in alpha stage until the interfaces, configuration formats, and API 
is settled.



Requirements
============

* PHP 5.3 or newer. If you use Ubuntu or Debian, you may need to install 
  the `php5-cli` package to let you run scripts on the command-line.

* Twig 1.6
* "dflydev"'s Markdown library
* Symfony2 Components: Yaml, Config, Finder, DependencyInjection, ClassLoader

A `composer.json` file is included to handle the installation of these 
requirements. See the next section for more about this.



Installation
============

1. [Download](https://github.com/inanimatt/site-builder/zipball/master) or clone this repository.
2. From the command-line, run `curl http://getcomposer.org/installer | php` and follow the on-screen instructions to install Composer.
3. Run `php composer.phar install` to install all the required libraries into the `vendor` folder.
4. Test the installation by running `php rebuild.php` and checking for files in the `output` folder.


Usage
=====

The basics
----------

1. Put your content (e.g. `index.php`, `about-me.md`) into the `content` 
   folder. Content files are just that: the main content of the page you want to 
   publish. The name of the file when you publish will be the same as the content 
   file, except with .html instead of the original extension.  
   You can create sub-directories in your content folder and they'll be created in 
   the output folder when you publish, so don't feel like you have to cram 
   everything into the same folder.

2. The default template is `template.php` in the `templates` folder. Change it 
   however you like. You can also change the default template to a Twig template 
   by changing `config.yml`. More on that later. The content of your content files 
   is placed into the template variable called `$content` (or `{{content}}` in 
   Twig).

3. Run `php rebuild.php` to render every file and save it to the `output` 
   folder.


Templates
=========

Using the built-in PHP templates
--------------------------------

The default template is a simple PHP template class based on one written by 
Chad Emrys Minick. Just write any old HTML, and where you want your content to 
be displayed, simply `<?php echo $content; ?>`. A simple function called `e()` 
is also provided as a shortcut to PHP's built-in `htmlspecialchars()` function, 
for output escaping.

Obviously you can run any other PHP code you like here, but bear in mind that 
this is a static site generator: the PHP you write will run once, during 
publishing, and the output will then be saved to a flat HTML file.


Using Twig templates
--------------------

Twig is a fast, clean, and extensible template language with a syntax very 
similar to Jinja and Django's templating systems. Read more about [writing Twig 
templates](http://twig.sensiolabs.org/doc/templates.html) here. 

Twig escapes output by default (the equivalent of calling the `e()` function
for every variable), which is very safe and good practice. However the
`content` variable which contains your page content probably shouldn't be
escaped. You can tell Twig not to escape it by passing it through the `raw`
filter, i.e. `{{ content | raw }}`



Content
=======

Site-builder accepts content in either HTML/PHP format, or Markdown format. 
Pick whichever you prefer, or use both.


HTML/PHP content format
-----------------------

Look at `content/example.php` for an example. The file is like any other HTML 
or PHP file and you can write whatever you like into it. The only difference is 
that a variable called `$view` is set before the file is processed, which 
allows you to pass more information to the template when you rebuild the site.

When the page is published, the file is run and the output is saved to the 
`$content` variable. Any other properties you set on `$view` will also be 
available within your template, so for example:


```php
<?php
    $view->title = "This is my page title";
?>
<h2>Hello world!</h2>
<p>This is my content file. There are many like it but this one is mine.</p>
```

Your template will have access to `$content`, which will contain `<h2>Hello 
world!<h2>…etc`, and `$title`, which will contain `This is my page title`.


If you set `$view->template` to the filename of a template in your `templates` 
folder, then Site-builder will render the page with that template instead of 
the default.


Markdown content format
-----------------------

Markdown is an "easy-to-read, easy-to-write plain text format", which is then 
turned into valid, clean HTML. It was developed by John Gruber and is very 
popular. [This 
page](https://raw.github.com/inanimatt/site-builder/master/README.md) itself 
was written in Markdown. A simple example markdown content file can be found in 
`content/markdown-example.md`.

Read more about [writing 
Markdown](http://daringfireball.net/projects/markdown/basics) here.

There are three important things to note about Markdown content files in 
Site-builder:

1. Markdown can contain plain old HTML, so don't feel constrained by it!
2. Your markdown content files should end in either `.markdown` or `.md`
3. Site-builder looks for (but doesn't require) a "front-matter" block where 
   you can set variables to be passed to your template.

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
front-matter block. The HTML content is set on the `$content` in PHP templates, 
and the `{{ content | raw }}` variable in Twig templates.



Notes
=====

* There are other Static Site Generators out there, many of them far more
  accomplished and capable than this one. For example 
  [Jekyll](http://jekyllrb.com/) (Ruby) and 
  [Hyde](http://ringce.com/hyde) (Python). YMMV!
* The content of each page is saved in the `$content` variable, but you can 
  set other variables too, which is handy, for example, for setting the page  
  title. Look at `content/example.php` for examples and ideas.
* You can also set the `template` variable to override the default template.
* You can change the output and content directories, the output file 
  extension, and the default template filename by editing `config.yml`
* `rebuild.php` is supposed to be a command-line tool. Don't put it on your 
  website. If you can't run php from the command-line and you're on a linux 
  system, try `apt-get install php-cli` or `yum install php5-cli` or 
  similar. 
* Your content can contain any PHP you like, but bear in mind that this tool 
  _really_ isn't intended for complex sites and will probably break them. If 
  you want to do more than set a few variables and wrap content in a 
  template, then I recommend you use a framework or microframework like 
  [Symfony2](http://symfony.com) or [Silex](http://silex-project.org).


Contributing
============

I'd love to have pull requests to improve Site-Builder. Please raise an issue 
first though, in case someone's already working on the feature.



Glaring omissions
-----------------

* A test suite. I'm work on this with PHPUnit and a code coverage tool,
  but I'd greatly appreciate any help or advice.
  
* Console output/logging. Right now you get some info if there's a problem,
  but if everything works properly, there's no output. Symfony's Console
  Component is a no-brainer for this, but something much more simple would 
  be fine too.

* Documentation (end user and developer). I've started to add doc-comments
  and there's this README, but there could be more and better.


Nice-to-haves:
--------------

* Introspection: it would be good if templates could get a list of other 
  templates, e.g. for building navigation. This would require building an 
  object with page titles/front-matter and setting it as something accessible 
  from the templates.

* Support for inline images. You can do it just fine right now by adding 
  resources to your output folder, but it'd be nicer if they were part of the 
  content and published, so you can wipe your output folder before rebuilding.

* Package the whole thing as a Phar, add help and command-line options with
  the Symfony Console component. 

* I'm fairly happy with the design of ContentCollection, ContentHandler and
  Serialiser interfaces, but the rendering stuff probably needs some 
  attention. Right now, the SiteBuilder class' renderFile method creates an
  SplFileInfo object to check the template file's extension (so it can pick
  the right renderer). This means that templates have to be file-based, 
  whereas the ContentHandler interface is flexible enough to allow content
  to be stored in files, or databases, or anywhere. This probably means
  creating a TemplateHandler interface, but it needs some thought and I'd
  welcome advice or help.
