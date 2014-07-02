<?php

namespace spec\Behat\SpawnerExtension\ServiceContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SpawnerExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Behat\SpawnerExtension\ServiceContainer\SpawnerExtension');
    }

    function it_is_behat_extension()
    {
        $this->shouldHaveType('Behat\Testwork\ServiceContainer\Extension');
    }

    function it_is_loadable(ContainerBuilder $container)
    {
        $this->load($container, array())->shouldBe(null);
    }
    
    function it_is_named_spawner()
    {
        $this->getConfigKey()->shouldReturn('spawner');
    }
}
