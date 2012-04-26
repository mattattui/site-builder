<?php

$sc = require __DIR__.'/src/bootstrap.php';

// Render the site with the given config file
try {
    $contentCollection = $sc->get('contentcollection');
    $builder = $sc->get('sitebuilder');
    $serialiser = $sc->get('serialiser');
    
    foreach($contentCollection->getObjects() as $content) {
        echo "Rendering ".$content->getRelativePathName();
        $output = $builder->renderFile($content);
        $serialiser->write($output, $content->getName());
        echo "... done".PHP_EOL;
    }
    
} catch (SiteBuilderException $e) {
    die($e->getMessage().PHP_EOL);
}