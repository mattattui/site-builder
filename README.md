What is(n't) this?
==================

Site-builder is a simple example of a static site generator.

It takes the files in `content/`, wraps them in a template you define, then
saves them in the `output/` folder. It's a way to get the benefits of 
templates (easy to maintain your content, easy to update your site style)
without the unnecessary overhead (i.e. using PHP to compile the content 
into the template every time someone visits the page).

PHP can be pretty fast, but unless you're savvy, it will prevent your site 
being cached by web browsers and proxies, and serve pages much slower than 
your web server can deal with plain old HTML files.

Don't get me wrong, PHP is great for certain tasks, but for adding header &
footer to a bunch of files, it's huge overkill. This script will give you
all the same advantages, and then a couple more.


Requirements
============

* PHP 5.3 or newer. If you use Ubuntu or Debian, you may need to install 
  the `php5-cli` package to let you run scripts on the command-line.


Usage
=====

1. Put your pages (e.g. `index.php`, `about-me.php`) into the `content`
   folder.
2. Edit `template.php` to your liking. Your pages' contents will be put into 
   the `$content` variable, so `echo` it where you want it to be displayed.
3. Run `php rebuild.php` to render every file and save it to the `output` 
   folder.


**Note**: this project works "out of the box" without the Markdown, YAML or
Twig dependencies, but you'll need to remove the markdown example from the 
`content/` folder otherwise the `rebuild.php` script will quit with an 
error.


Twig support (optional)
=======================

A `composer.json` file is provided to download and install Twig. Download
[Composer](http://getcomposer.org/download/) and run `php composer.phar install` to
download and set up Twig and anything it might require.

If Twig is installed and the template is set to a file with the `.twig`
extension, then Site-Builder will automatically to render the template using
Twig. You can use Twig templates for just some of your pages by setting the
`$view->template` property in the head of each page, or change the default
template to a Twig template in `config.ini`

Twig escapes output by default (the equivalent of calling the `e()` function
for every variable), which is very safe and good practice. However the
`content` variable which contains your page content probably shouldn't be
escaped. You can tell Twig not to escape it by passing it through the `raw`
filter, i.e. `{{ content | raw }}`


Markdown support (optional)
===========================

A `composer.json` file is provided to download and install Markdown. Download
[Composer](http://getcomposer.org/download/) and run `php composer.phar install` to
download and set up Markdown and the Yaml library required for setting
variables in a front-matter block.

If Markdown and Yaml are installed, Site-Builder will automatically transform
any files in the `content` directory that end with a `.md` extension using
Markdown. You may use a "front matter" block in the YAML format to set other
variables that will be passed to the template. Look at
content/markdown-example.md for a simple example.

You may use Markdown content with Twig or PHP templates; it's not fussy.


Notes
=====

* There are other Static Site Generators out there, many of them far more
  accomplished and capable than this one. For example 
  [Jekyll](http://jekyllrb.com/) (Ruby) and 
  [Hyde](http://ringce.com/hyde) (Python). I use Site-Builder because it
  has no required dependencies, and it meets my very limited needs. YMMV!
* I wrote this after an evening at the pub. Use at your own risk! It may be 
  rewritten heavily the next time I see it :)
* The content of each page is saved in the `$content` variable, but you can 
  set other variables too, which is handy, for example, for setting the page  
  title. Look at `content/example.php` for examples and ideas.
* You can also set the `template` variable to override the default template.
* You can change the output and content directories, the output file 
  extension, and the default template filename by editing `config.ini`
* `rebuild.php` is supposed to be a command-line tool. Don't put it on your 
  website. If you can't run php from the command-line and you're on a linux 
  system, try `apt-get install php-cli` or `yum install php5-cli` or 
  similar. 
* Your content can contain any PHP you like, but bear in mind that this tool 
  _really_ isn't intended for complex sites and will probably break them. If 
  you want to do more than set a few variables and wrap content in a 
  template, then I recommend you use a framework or microframework like 
  [Symfony2](http://symfony.com) or [Silex](http://silex-project.org).

