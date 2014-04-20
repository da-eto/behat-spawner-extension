<?php

namespace DaEtoYa\Behat\SpawnerExtension\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Process\ProcessBuilder;

class SuiteListener implements EventSubscriberInterface
{
    private $commands;
    /** @var string */
    private $winPrefix;
    /** @var string */
    private $nixPrefix;
    /** @var string */
    private $workingDirectory;
    /** @var array|\Symfony\Component\Process\Process[] */
    private $processes = array();

    /**
     * Construct listener
     *
     * @param array  $commands         commands in array format
     * @param null   $workingDirectory working directory for commands
     * @param string $nixPrefix        prefix for *nix-based OS, default "exec"
     * @param string $winPrefix        prefix for Windows OS
     */
    public function __construct($commands = array(), $workingDirectory = null, $nixPrefix = "exec", $winPrefix = "")
    {
        $this->commands = $commands;
        $this->nixPrefix = $nixPrefix;
        $this->winPrefix = $winPrefix;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            'beforeSuite' => array('spawnProcesses', -20),
            'afterSuite' => array('stopProcesses', -20),
        );
    }

    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @return string
     */
    public function getWinPrefix()
    {
        return $this->winPrefix;
    }

    /**
     * @return string
     */
    public function getNixPrefix()
    {
        return $this->nixPrefix;
    }

    /**
     * @return null
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Spawns processes
     */
    public function spawnProcesses()
    {
        if (!count($this->processes)) {
            $workingDirectory = $this->workingDirectory ? $this->workingDirectory : ".";
            $execPrefix = defined('PHP_WINDOWS_VERSION_BUILD') ? $this->winPrefix : $this->nixPrefix;

            foreach ($this->commands as $arguments) {
                $builder = new ProcessBuilder();
                $builder->setWorkingDirectory($workingDirectory);
                $builder->setPrefix($execPrefix);

                foreach ($arguments as $arg) {
                    $builder->add($arg);
                }

                $process = $builder->getProcess();
                $process->start();
                $this->processes[] = $process;
            }
        }
    }

    /**
     * Stops processes
     */
    public function stopProcesses()
    {
        foreach ($this->processes as $process) {
            $process->stop();
        }

        $this->processes = array();
    }

    /**
     * @return array|\Symfony\Component\Process\Process[]
     */
    public function getProcesses()
    {
        return $this->processes;
    }
}
