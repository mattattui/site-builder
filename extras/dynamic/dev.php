<?php
require __DIR__.'/../vendor/autoload.php';

define('SITEBUILDER_ROOT', __DIR__.'/..');
chdir(SITEBUILDER_ROOT);
$container = require __DIR__.'/../vendor/inanimatt/site-builder/src/bootstrap.php';

use Inanimatt\SiteBuilder\Event\FileCopyEvent;
use Inanimatt\SiteBuilder\FilesystemEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

$request = Request::createFromGlobals();

// Resolve the URL into a filename
$path = $request->getRequestUri();

if ($path == '/') {
    $path = '/index.html';
}

$filename = __DIR__.'/../content'.$path;

/* Normalise filename:
 *  - replace .php with .html if found
 *  - append .html if missing
 */

if (preg_match('/\.php$/', $filename)) {
    $filename = substr($filename, 0, -4).'.html';
}

if (strpos($filename, '.html') === false) {
    $filename .= '.html';
}

/* Search for a corresponding content file. Either:
 * 1. The actual filename requested
 * 2. The filename with a .md or .markdown extension instead of .html
 * Stop on the first matching file found.
 */
$search = array($filename);
$search[] = substr($filename, 0, -5).'.md';
$search[] = substr($filename, 0, -5).'.markdown';

$notfound = true;

foreach($search as $fn) {
    if (is_file($fn)) {
        $filename = $fn;
        $notfound = false;
        break;
    }
}

// Nothing found? Return a 404.
if ($notfound) {
    $filename = __DIR__.'/../content/404.html';
}

/* Ensure requested file is actually in the content folder.
 * You'd think I'd do this sooner, but we don't know the actual
 * filename before this point.
 */
$filename = realpath($filename);

if (strpos($filename, realpath(__DIR__.'/../content')) !== 0) {
    // Attempt to read file outside of content folder
    $filename = __DIR__.'/../content/404.html';
}


/* Render the file:
 * Dispatch a FileCopyEvent to call transformers on a file, then build a response
 */

$event = new FileCopyEvent($filename, $filename);
$extension = $event->getExtension();

$event = $container->get('event_dispatcher')->dispatch(FilesystemEvents::COPY, $event);

$response = new Response($event->getContent(), 200);

$headers = $event->data->get('headers')->getOrElse(array());

// Special cases
if (isset($headers['location'])) {
    $response = new RedirectResponse($headers['location']);
    unset($headers['location']);
}
if (isset($headers['status'])) {
    $response->setStatusCode($headers['status']);
    unset($headers['status']);
}

// Everything else
foreach ($headers as $key => $value) {
    $response->headers->set($key, $value);
}

$response->send();
