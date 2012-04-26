<?php

$sc = require __DIR__.'/src/bootstrap.php';

// Render the site with the given config file


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application('SiteBuilder', '2.0-dev');

$console
    ->register('rebuild')
    ->setDescription('Renders all content, writes to output folder')
    ->setHelp(<<<'EOH'
This command renders your content files and saves them in the output folder.
Run it like this:

        %command.full_name%
EOH
)
    ->setDefinition(array())
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($sc) {
        $contentCollection = $sc->get('contentcollection');
        $builder = $sc->get('sitebuilder');
        $serialiser = $sc->get('serialiser');
        
        foreach($contentCollection->getObjects() as $content) {
            $output->writeln(sprintf('Rendering <info>%s</info>', $content->getRelativePathName()));

            $out = $builder->renderFile($content);
            $serialiser->write($out, $content->getName());
        }
    })
;
    
$console->run();
