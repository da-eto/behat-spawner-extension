<?php

namespace spec\DaEtoYa\Behat\SpawnerExtension;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('DaEtoYa\Behat\SpawnerExtension\Extension');
    }

    function it_is_behat_extension()
    {
        $this->shouldHaveType('Behat\Behat\Extension\ExtensionInterface');
    }

    function it_defines_no_compiler_passes()
    {
        $this->getCompilerPasses()->shouldHaveCount(0);
    }

    function it_is_configurable(ArrayNodeDefinition $builder)
    {
        $this->getConfig($builder)->shouldBe(null);
    }

    function it_is_loadable(ContainerBuilder $container)
    {
        $this->load(array(), $container)->shouldBe(null);
    }
}
