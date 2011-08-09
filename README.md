What is(n't) this?
==================

If the only reason you use PHP is to put a header and footer on all your
pages, use this script instead.

It will read all the files in the `content` directory, wrap each one with
the `template.php` file, and save it in the `output` directory with a
`.html` filename, ready to upload to your server. PHP can be pretty fast,
but unless you're savvy, it will prevent your site being cached by web
browsers and proxies, and serve pages much slower than your web server can
deal with plain old HTML files.

Don't get me wrong, PHP is great for certain tasks, but for adding header &
footer to a bunch of files, it's huge overkill. This script will give you
all the same advantages, and then a couple more.


Usage
=====

1. Put your pages (e.g. `index.php`, `about-me.php`) into the `content`
   folder.
2. Edit `template.php` to your liking. Your pages' contents will be put into 
   the `$content` variable, so `echo` it where you want it to be displayed.
3. Run `php rebuild.php` to render every file and save it to the `output` 
   folder.


Notes
=====

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

