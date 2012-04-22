<?php

$sc = require __DIR__.'/src/bootstrap.php';

// Render the site with the given config file
try {
    $builder = $sc->get('sitebuilder');
    $builder->renderSite();
} catch (SiteBuilderException $e) {
    die($e->getMessage().PHP_EOL);
}