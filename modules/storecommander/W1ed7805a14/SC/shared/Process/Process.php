<?php

namespace Sc\service\Process;

use Exception;
use Sc\service\Process\Traits\ProcessTrait;

class Process
{
    use ProcessTrait;
    /**
     * @var mixed
     */
    private $method;
    /**
     * @var array
     */
    private $arguments = [];
    /**
     * @var ProcessInterface
     */
    private $process;
    /**
     * @var mixed
     */
    private $stepName;
    /**
     * @var mixed
     */
    private $stepProgress;
    /**
     * @var array
     */
    private $data;
    /**
     * @var ProcessInterface
     */
    private $page = 0;
    /**
     * @var mixed
     */
    private $delayOnTerminate = 0;
    /**
     * @var string
     */
    private $id;
    /**
     * @var mixed
     */
    private $total = null;
    /**
     * @var mixed
     */
    private $batchSize = 1;

    public function __construct(ProcessInterface $process)
    {
        $this->process = $process;
    }

    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethodArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function getMethodArguments()
    {
        return $this->arguments;
    }

    public function getStepName()
    {
        return $this->stepName;
    }

    public function setStepProgress($stepProgress)
    {
        $this->stepProgress = $stepProgress;

        return $this;
    }

    public function getStepProgress()
    {
        if ($this->getTotal())
        {
            $this->stepProgress = ($this->stepProgress / $this->getTotal()) * ($this->getBatchSize() / $this->getTotal());
        }

        return $this->stepProgress ?: 1;
    }

    /**
     * @return Process
     */
    public function run()
    {
        $continue = true;
        while ($continue)
        {
            try
            {
                if (connection_aborted())
                { // vérification que la connexion est toujours active
                    $continue = false; // la connexion est coupée, arrêt des traitements
                }
                $method = $this->method;
                // TODO : gérer les parametres pour ne pas forcer le paramètre page en fin de tableau
                $methodArguments = $this->arguments;
                $methodArguments[] = $this->getPage();

                $this->data = (array) $this->process->$method(...$methodArguments);
                // si pas de données : on stoppe la boucle
                if (count($this->data) === 0)
                {
                    usleep($this->getDelayOnTerminate());
                    $continue = false;
                }
                // si methode spécifique pour arrêter la boucle définie
                if (method_exists($this->process, 'terminateOn'))
                {
                    $continue = $this->process->terminateOn($this->data, $this->getPage()); // TODO 2 a revoir : getPage() spécifique à pagination
                }
                $message = $this->process->getProcessMessageForIteration($this->getPage(), count($this->data), $this->getMethod(), $this->getMethodArguments());
                $this->sendResponse('process_started', $this->getData($message));
                $this->incrementPage();
                ob_flush();
                ob_end_flush();
                flush();
            }
            catch (Exception $e)
            {
                if (method_exists($this, 'getService'))
                {
                    $this->getService()->addError($e);
                }
                $this->process->sendResponse('error', $e->getMessage());
            }
        }
        $this->process->sendResponse('process_completed', $this->process->getProcessMessageCompleted('done'));

        return $this;
    }

    /**
     * @desc id process composé de la classe + methode + arguments
     *
     * @return string
     */
    public function getId()
    {
        if (!$this->id)
        {
            $arguments = $this->getMethodArguments();
            $arguments[] = get_class($this->process);
            $arguments[] = $this->getMethod();
            $this->id = md5(json_encode($arguments));
        }

        return $this->id;
    }

    /**
     * @return array
     */
    public function getData($message = '')
    {
        return [
            'processId' => $this->getId(),
            'processIteration' => $this->getPage(),
            'stepName' => $this->getStepName(),
            'stepProgress' => $this->getStepProgress(),
            'message' => $message,
        ];
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $offset
     */
    public function setPage($offset)
    {
        $this->page = (int) $offset;

        return $this;
    }

    /**
     * @return Process
     */
    public function incrementPage()
    {
        ++$this->page;

        return $this;
    }

    /**
     * @param int $delay microseconds
     *
     * @return Process
     */
    public function setDelayOnTerminate($delay)
    {
        $this->delayOnTerminate = $delay;

        return $this;
    }

    /**
     * @return float
     */
    public function getDelayOnTerminate()
    {
        return $this->delayOnTerminate;
    }

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    public function setBatchSize($batchSize)
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * @return int|mixed
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }
}
