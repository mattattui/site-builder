<?php
namespace Inanimatt\SiteBuilder;

use Inanimatt\SiteBuilder\FilesystemEvents;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('event_dispatcher')) {
            return;
        }

        $definition = $container->getDefinition(
            'event_dispatcher'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'sitebuilder.transformer'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addListener',
                    array(FilesystemEvents::COPY, array(new Reference($id), 'transform'), $attributes['priority'])
                );

            }
        }
    }
}
