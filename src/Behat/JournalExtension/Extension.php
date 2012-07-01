<?php

namespace Behat\JournalExtension;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

use Behat\Behat\Extension\ExtensionInterface;

class Extension implements ExtensionInterface
{
    function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.xml');

        $container->setParameter('behat.formatter.dispatcher.journal.capture_all', $config['capture_all']);
    }

    function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->booleanNode('capture_all')
                    ->defaultValue(false)
                ->end()
            ->end()
        ;
    }

    function getCompilerPasses()
    {
        return array();
    }
}
