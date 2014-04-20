<?php

namespace spec\DaEtoYa\Behat\SpawnerExtension\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SuiteListenerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array(), null, "", "");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('DaEtoYa\Behat\SpawnerExtension\Listener\SuiteListener');
    }

    function it_is_event_subscriber()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_beforeSuite_and_afterSuite_events()
    {
        $this::getSubscribedEvents()->shouldHaveKey('beforeSuite');
        $this::getSubscribedEvents()->shouldHaveKey('afterSuite');
    }

    function it_assign_commands_on_construct()
    {
        $commands = array(array("vim", "test.txt"), array("hi"));

        $this->beConstructedWith($commands, null, "", "");
        $this->getCommands()->shouldBe($commands);
    }

    function it_assign_working_directory_on_construct()
    {
        $workingDirectory = "./bin";

        $this->beConstructedWith(array(), $workingDirectory, "", "");
        $this->getWorkingDirectory()->shouldBe($workingDirectory);
    }

    function it_assign_prefixes_on_construct()
    {
        $winPrefix = "win";
        $nixPrefix = "nix";

        $this->beConstructedWith(array(), null, $nixPrefix, $winPrefix);
        $this->getNixPrefix()->shouldBe("nix");
        $this->getwinPrefix()->shouldBe("win");
    }

    function it_has_void_function_spawnProcesses()
    {
        $this->spawnProcesses()->shouldBe(null);
    }

    function it_has_void_function_stopProcesses()
    {
        $this->stopProcesses()->shouldBe(null);
    }

    function it_should_create_processes_one_on_each_command()
    {
        $commands = array(array("php", "-v"), array("php", "-v"));

        $this->beConstructedWith($commands, null);
        $this->createProcesses();
        $this->getProcesses()->shouldHaveCount(count($commands));
    }

    function it_should_store_processes_between_spawn_and_stop_and_clear_after()
    {
        $commands = array(array("php", "-v"), array("php", "-v"));

        $this->beConstructedWith($commands, null);
        $this->getProcesses()->shouldHaveCount(0);
        $this->spawnProcesses();
        $this->getProcesses()->shouldHaveCount(count($commands));
        $this->stopProcesses();
        $this->getProcesses()->shouldHaveCount(0);
    }

    public function getMatchers()
    {
        return array(
            'haveKey' => function ($subject, $key) {
                return array_key_exists($key, $subject);
            },
        );
    }
}
