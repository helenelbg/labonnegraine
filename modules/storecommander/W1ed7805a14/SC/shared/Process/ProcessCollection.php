<?php

namespace Sc\service\Process;

use Sc\service\Process\Traits\ProcessWithPaginationTrait;
use shared\ScLogger\Traits\ScLoggerTrait;

class ProcessCollection
{
    use ProcessWithPaginationTrait;
    use ScLoggerTrait;

    /**
     * @var array
     */
    private $processes;
    /**
     * @var mixed
     */
    private $start_process;
    /**
     * @var mixed
     */
    private $start_iteration;
    private $delayBetweenProcesses = 0;
    /**
     * @var array
     */
    private $onComplete = [];

    public function __construct($startProcess = null, $startIteration = null)
    {
        $this->processes = [];
        $this->start_process = $startProcess;
        $this->start_iteration = $startIteration;
    }

    /**
     * @return ProcessCollection
     */
    public function add(Process $process)
    {
        $this->processes[$process->getId()] = $process;

        return $this;
    }

    /**
     * @return ProcessCollection
     */
    public function remove(Process $process)
    {
        unset($this->processes[$process->getId()]);

        return $this;
    }

    /**
     * @return void
     */
    public function run()
    {
        $processesInCollection = count($this->getProcesses());
        $currentStep = 0;
        $this->getLogger()->debug('[PROCESS START] process Collection');
        /** @var Process $process */
        foreach ($this->getProcesses() as $process)
        {
            $this->getLogger()->debug('[PROCESS RUNNING] '.$process->getStepName());

            $process->setPage($this->getStartIteration())
                ->setBatchSize($this->getBatchSize())
                ->setLastRunAt($this->getLastRunAt())
                ->setStepProgress($currentStep++ / $processesInCollection * 100)
                ->setDelayOnTerminate($this->getDelayBetweenProcesses())
                ->run();

            $this->getLogger()->debug('[PROCESS DONE] '.$process->getStepName());
        }

        call_user_func($this->getOnComplete());

        $this->sendResponse('done');
        exit;
    }

    /**
     * @return mixed|null
     */
    public function getStartProcess()
    {
        return $this->start_process;
    }

    /**
     * @return mixed|null
     */
    public function getStartIteration()
    {
        return $this->start_iteration;
    }

    /**
     * @description liste des process à traiter à partir du processId retourné par getStartProcess()
     *
     * @return array
     */
    public function getProcesses()
    {
        if ($this->getStartProcess())
        {
            $key = array_search($this->getStartProcess(), array_keys($this->processes), true);
            if ($key !== false)
            {
                $this->processes = array_slice($this->processes, $key, count($this->processes), true);
            }
        }

        return $this->processes;
    }

    /**
     * @param int $delayBetweenProcesses microseconds
     */
    public function setDelayBetweenProcesses($delayBetweenProcesses)
    {
        $this->delayBetweenProcesses = $delayBetweenProcesses;

        return $this;
    }

    public function onComplete($class, $method)
    {
        $this->onComplete = [$class, $method];

        return $this;
    }

    /**
     * @return array
     */
    public function getOnComplete()
    {
        return $this->onComplete;
    }

    private function getDelayBetweenProcesses()
    {
        return $this->delayBetweenProcesses;
    }
}
