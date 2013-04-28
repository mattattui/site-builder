# Dynamic content creation

This script uses Sitebuilder to generate pages on request. You can run it from an empty `output` directory and it'll behave as if you've done a full `php sitebuilder.php rebuild` - loading the file from the content directory, transforming it, then serving it to the browser. It won't rebuild files that already exist, so you can mix static and dynamic content.

It's not *really* recommended for production use because it kind of defeats the point of using a static site generator, but it's handy for developing templates or testing content without needing to rebuild every time you make a change.

It can also dynamically set HTTP headers from frontmatter blocks, which means you can use this to build a 404 page, or redirect content to another URL.

## How to set it up:

* This doesn't work with the `sitebuilder.phar` build, only the full download with the composer setup.

* Copy `dev.php` and `.htaccess` to the output folder - careful not to overwrite your own, if you have one. The `.htaccess` redirects all requests for non-existent files to `dev.php`, which will then attempt to create them.

* If you're not using Apache with `mod_rewrite` then I'm afraid you're on your own. Roughly speaking, the nginx equivalent would look like this (assuming PHP is already set up):

        location ~ \.html$ {
            try_files $uri $uri/ /dev.php;
        }

* Visit e.g. `http://yoursite.com/some-page.html`

* To set HTTP headers, add a `headers` section to your content's frontmatter block:

```yaml
---
headers:
    status: 301 # Just the number; the script adds the rest
    Location: /some-other-page.html
    Content-Type: text/html;charset=utf-8
---
```

## Performance

I tried to make sure it's not embarrassingly slow, although since it's primarily for development, it doesn't cache templates by default. You can fix that by changing the `cache` setting from `false` to a directory name in `config.ini` - make sure it's writeable by the web server. You might also want to optimise the composer autoloader by running `php composer.phar dump-autoload --optimize`. 

Honestly though, if you're finding it to be too slow, then use `php sitebuilder.php rebuild` instead - that's what it's for. :)
