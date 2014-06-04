<?php

namespace DaEtoYa\Behat\SpawnerExtension;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
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
     * @param ContainerBuilder $container ContainerBuilder instance
     * @param array            $config Extension configuration hash (from behat.yml)
     */
    public function load(ContainerBuilder $container, array $config)
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
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * Returns the extension config key.
     *
     * @return string
     */
    public function getConfigKey()
    {
        return 'spawner';
    }

    /**
     * Initializes other extensions.
     *
     * This method is called immediately after all extensions are activated but
     * before any extension `configure()` method is called. This allows extensions
     * to hook into the configuration of other extensions providing such an
     * extension point.
     *
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * Setups configuration for the extension.
     *
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
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
}
