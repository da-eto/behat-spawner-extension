<?php

namespace Behat\SpawnerExtension\Listener;

use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Process\ProcessBuilder;

class SuiteListener implements EventSubscriberInterface
{
    /** @var array */
    private $commands;
    /** @var string */
    private $winPrefix;
    /** @var string */
    private $nixPrefix;
    /** @var string */
    private $workingDirectory;
    /** @var array|\Symfony\Component\Process\Process[] */
    private $processes = array();
    /** @var int */
    private $sleep = 0;

    /**
     * Construct listener
     *
     * @param array  $commands commands in array format
     * @param null   $workingDirectory working directory for commands
     * @param string $nixPrefix prefix for *nix-based OS, default "exec"
     * @param string $winPrefix prefix for Windows OS
     * @param int    $sleep sleep after spawn (in milliseconds)
     */
    public function __construct(
        $commands = array(),
        $workingDirectory = null,
        $nixPrefix = "exec",
        $winPrefix = "",
        $sleep = 0
    ) {
        $this->commands = $commands;
        $this->nixPrefix = $nixPrefix;
        $this->winPrefix = $winPrefix;
        $this->workingDirectory = $workingDirectory;
        $this->sleep = $sleep;
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
            SuiteTested::BEFORE => array('spawnProcesses', -20),
            SuiteTested::AFTER => array('stopProcesses', -20),
        );
    }

    /**
     * @return array
     */
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
     * @return string|null
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
        if (count($this->processes)) {
            return;
        }

        $workingDirectory = $this->getNormalizedWorkdir();
        $execPrefix = $this->getPlatformPrefix();

        foreach ($this->commands as $arguments) {
            $process = $this->createProcess($arguments, $execPrefix, $workingDirectory);
            $process->start();
            $this->processes[] = $process;
        }

        $this->sleepIfSpawned();
    }

    /**
     * Normalize working dir (set to '.' if empty)
     *
     * @return string
     */
    private function getNormalizedWorkdir()
    {
        if ($this->workingDirectory) {
            return $this->workingDirectory;
        } else {
            return ".";
        }
    }

    /**
     * Get prefix based on current platform (Windows/*-nix)
     *
     * @return string
     */
    private function getPlatformPrefix()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return $this->winPrefix;
        } else {
            return $this->nixPrefix;
        }
    }

    /**
     * @param array  $arguments
     * @param string $execPrefix
     * @param string $workingDirectory
     *
     * @return \Symfony\Component\Process\Process
     */
    private function createProcess($arguments, $execPrefix, $workingDirectory)
    {
        $builder = new ProcessBuilder();
        $builder->setWorkingDirectory($workingDirectory);
        $builder->setPrefix($execPrefix);

        foreach ($arguments as $arg) {
            $builder->add($arg);
        }

        return $builder->getProcess();
    }

    /**
     * Sleep if processes has been spawned and sleep option configured
     */
    private function sleepIfSpawned()
    {
        if ($this->sleep > 0 && count($this->processes)) {
            usleep(1000 * $this->sleep);
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

    /**
     * @return int
     */
    public function getSleep()
    {
        return $this->sleep;
    }
}
