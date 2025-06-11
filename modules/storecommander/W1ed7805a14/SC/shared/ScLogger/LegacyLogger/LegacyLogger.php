<?php

namespace Sc\ScLogger\LegacyLogger;



// TODO : schema + renommer en legacy + recup interface psr logger

use Sc\ScLogger\ScLogger;

class LegacyLogger
{
    /**
     * @var mixed
     */
    public $logDir;
    /**
     * @var int|mixed
     */
    private $logFilesToKeep;
    /**
     * @var array|string
     */
    private $fileInfos;

    public function __construct($fileName, $logFilesToKeep = 10)
    {
        $this->fileInfos = pathinfo($fileName);
        $this->logDir = $this->fileInfos['dirname'];
        $this->logFilesToKeep = $logFilesToKeep;
    }

    public function log($level, $message, array $context = array())
    {

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir,0750);
        }
        $fi = new \FilesystemIterator($this->logDir, \FilesystemIterator::SKIP_DOTS);
        if ($fi && iterator_count($fi) > $this->logFilesToKeep) {
            // TODO 2 remove old files
        }
        $log = '['.date('Y-m-d H:i:s').'] '.ScLogger::getLevelValue($level).': '.$message.PHP_EOL;
        file_put_contents($this->logDir.'/'.$this->fileInfos['filename'].'-'.date('Y-m-d').'.'.$this->fileInfos['extension'], $log, FILE_APPEND);

    }



}
