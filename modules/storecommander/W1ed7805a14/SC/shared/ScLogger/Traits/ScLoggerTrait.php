<?php

namespace shared\ScLogger\Traits;

use Sc\ScLogger\ScLogger;

trait ScLoggerTrait
{
    /**
     * @var mixed
     */
    private $logger = null;
    /**
     * @return mixed
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger(ScLogger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

}

