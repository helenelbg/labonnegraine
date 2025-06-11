<?php

namespace Sc\ScLogger;

// TODO 2 : mise en place autoloader
require __DIR__.'/LegacyLogger/LegacyLogger.php';

use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\DeduplicationHandler;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Sc\ScLogger\LegacyLogger\LegacyLogger;

class ScLogger
{
    /**
     * Detailed debug information.
     */
    const DEBUG = 100;
    /**
     * Interesting events.
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;
    /**
     * Uncommon events.
     */
    const NOTICE = 250;
    /**
     * Exceptional occurrences that are not errors.
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;
    /**
     * Runtime errors.
     */
    const ERROR = 400;
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;
    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;
    /**
     * Logging levels from syslog protocol defined in RFC 5424.
     *
     * @var array Logging levels
     */
    protected static $levels = [
        self::DEBUG => 'DEBUG',
        self::INFO => 'INFO',
        self::NOTICE => 'NOTICE',
        self::WARNING => 'WARNING',
        self::ERROR => 'ERROR',
        self::CRITICAL => 'CRITICAL',
        self::ALERT => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    ];

    /**
     * @var int
     */
    private $logLevel;
    private $filesToKeep;
    private $logFile;
    /**
     * @var mixed
     */
    private $name;
    /**
     * @var array
     */
    private $handlers;
    /**
     * @var array
     */
    private $processors;
    private $logger;

    /**
     * @desc sc logger adapter
     *
     * @param $name
     */
    public function __construct($name = 'sc', array $handlers = [], array $processors = [])
    {
        $this->name = $name;
        $this->logFile = SC_TOOLS_DIR.'log/'.$name.'.log';
        $this->handlers = $handlers;
        $this->processors = $processors;
        $this->logger = $this->initLogger($name);
    }

    /**
     * @return array|string[]
     */
    public static function getLevels()
    {
        return self::$levels;
    }

    /**
     * @return array|string[]
     */
    public static function getLevelValue($code)
    {
        return self::$levels[$code];
    }

    private function initLogger($name)
    {
        if (!class_exists('\Monolog\Logger'))
        {
            return new LegacyLogger($this->getLogFile(), $this->getFilesToKeep());
        }

        $logger = new Logger($this->name, $this->handlers, $this->processors);

        // rotating log file handler
        $rotatingLogFileHandler = new RotatingFileHandler($this->getLogFile(), $this->getFilesToKeep(), self::DEBUG);
        $fingers = new FingersCrossedHandler($rotatingLogFileHandler, new ErrorLevelActivationStrategy(self::DEBUG));
        // avoid deduplication (20s)
        $deduplicationHandler = new DeduplicationHandler($fingers, null, self::ERROR, 20, true);
        $logger->pushHandler($deduplicationHandler);

//        $formatter = new HtmlFormatter();
//        $rotatingHtmlFileHandler = new RotatingFileHandler($this->getLogFile('html'), $this->getFilesToKeep(), $this->getLogLevel());
//        $rotatingHtmlFileHandler->setFormatter($formatter);
//        $fingers = new FingersCrossedHandler($rotatingHtmlFileHandler, new ErrorLevelActivationStrategy(self::ERROR));
//        $logger->pushHandler($fingers);

        // chromePHP browser extension handler
        $ChromePHPHandler = new ChromePHPHandler(self::DEBUG, true);
        $logger->pushHandler($ChromePHPHandler);

        // FirePHP browser extension handler
//        $FirePHPHandler = new FirePHPHandler(self::DEBUG, true);
//        $logger->pushHandler($FirePHPHandler);

        return $logger;
    }

    /**
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logLevel ?: self::DEBUG;
    }

    /**
     * @param int $logLevel
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;

        return $this;
    }

    public function emergency($message, array $context = [])
    {
        $this->logger->log(self::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(self::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(self::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(self::WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(self::NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(self::INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(self::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }

    public function getFilesToKeep()
    {
        return $this->filesToKeep ?: 5;
    }

    /**
     * @param int $filesToKeep number of files to keep
     */
    public function setFilesToKeep($filesToKeep)
    {
        $this->filesToKeep = $filesToKeep;

        return $this;
    }

    public function getLogFile($extension = '')
    {
        $fileInfo = pathinfo($this->logFile);
        $fileInfo['extension'] = $extension ? $extension : $fileInfo['extension'];

        return $fileInfo['dirname'].'/'.$fileInfo['filename'].'.'.$fileInfo['extension'];
    }
}
