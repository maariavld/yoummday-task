<?php

namespace App\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Contracts\Service\Attribute\Required;

class MonologLogger
{
    #[Required]
    private function setLogger(Logger $logger): Logger
    {
        $YDLogger = $logger;
        $streamHandler = new StreamHandler('php://stdout', Level::Debug);
        $YDLogger->pushHandler($streamHandler);
        return $YDLogger;
    }

    public function getLogger(): Logger
    {
        return $this->setLogger(new Logger('app'));
    }
}