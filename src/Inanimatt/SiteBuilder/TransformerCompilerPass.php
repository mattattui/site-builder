<?php
namespace Inanimatt\SiteBuilder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sitebuilder_filesystem')) {
            return;
        }

        $definition = $container->getDefinition(
            'sitebuilder_filesystem'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'sitebuilder.transformer'
        );
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addTransformer',
                array(new Reference($id))
            );
        }
    }
}
