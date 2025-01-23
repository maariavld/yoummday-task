<?php

namespace App\Logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonologLogger
{
    public function __construct(protected Logger $logger)
    {
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function infoLog(string $logMessage): void
    {
        $this->logger = $this->setHandler("php://stdout", Level::Info);
        $this->logger->info($logMessage);
    }

    public function errorLog(string $logMessage): void
    {
        $this->logger = $this->setHandler("php://stderr", Level::Error);
        $this->logger->error($logMessage);
    }

    private function setHandler(string $stream, Level $level): Logger
    {
        $handler = new StreamHandler($stream, $level);
        $handler->setFormatter(new JsonFormatter());
        return $this->logger->pushHandler($handler);
    }
}
