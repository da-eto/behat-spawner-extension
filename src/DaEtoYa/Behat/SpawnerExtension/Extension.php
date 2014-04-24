<?php

namespace DaEtoYa\Behat\SpawnerExtension;

use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Extension implements ExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . DIRECTORY_SEPARATOR . 'services')
        );

        $loader->load('services.yml');

        $config['commands'] = isset($config['commands']) ? $config['commands'] : array();
        $config['work_dir'] = isset($config['work_dir']) ? $config['work_dir'] : null;
        $config['nix_prefix'] = isset($config['nix_prefix']) ? $config['nix_prefix'] : 'exec';
        $config['win_prefix'] = isset($config['win_prefix']) ? $config['win_prefix'] : '.';
        $config['sleep'] = isset($config['sleep']) ? $config['sleep'] : 0;

        $container->setParameter('behat.spawner.commands', $config['commands']);
        $container->setParameter('behat.spawner.working_directory', $config['work_dir']);
        $container->setParameter('behat.spawner.nix_prefix', $config['nix_prefix']);
        $container->setParameter('behat.spawner.win_prefix', $config['win_prefix']);
        $container->setParameter('behat.spawner.sleep', $config['sleep']);
    }

    /**
     * Setups configuration for current extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->variableNode('commands')
                ->end()
                ->scalarNode('win_prefix')
                    ->defaultValue('')
                ->end()
                ->scalarNode('work_dir')
                    ->defaultValue('.')
                ->end()
                ->scalarNode('nix_prefix')
                    ->defaultValue('exec')
                ->end()
                ->integerNode('sleep')
                    ->defaultValue(0)
                ->end()
            ->end();
    }

    /**
     * Returns compiler passes used by this extension.
     *
     * @return array
     */
    public function getCompilerPasses()
    {
        return array();
    }
}
