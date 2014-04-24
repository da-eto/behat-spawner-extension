<?php

namespace DaEtoYa\Behat\SpawnerExtension;

use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Extension implements ExtensionInterface
{
    /** @var array Default options for configuration */
    private $defaultOptions = array(
        'commands' => array(),
        'work_dir' => '.',
        'win_prefix' => '',
        'nix_prefix' => 'exec',
        'sleep' => 0,
    );

    /**
     * Loads a specific configuration.
     *
     * @param array            $config Extension configuration hash (from behat.yml)
     * @param ContainerBuilder $container ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . DIRECTORY_SEPARATOR . 'services')
        );

        $loader->load('services.yml');

        $config = array_merge($this->defaultOptions, $config);

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
                    ->defaultValue($this->defaultOptions['win_prefix'])
                ->end()
                ->scalarNode('work_dir')
                    ->defaultValue($this->defaultOptions['work_dir'])
                ->end()
                ->scalarNode('nix_prefix')
                    ->defaultValue($this->defaultOptions['nix_prefix'])
                ->end()
                ->integerNode('sleep')
                    ->defaultValue($this->defaultOptions['sleep'])
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
