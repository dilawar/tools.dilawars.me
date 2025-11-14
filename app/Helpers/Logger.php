<?php

namespace App\Helpers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\IntrospectionProcessor;
use Psr\Log\LoggerInterface;

final class Logger
{
    /**
     * @var array<string, mixed>
     */
    private static array $instances = [];

    private static function getInstance(string $loggerName = 'dilawars.me'): LoggerInterface
    {
        if (!isset(self::$instances[$loggerName])) {
            $inst = new self();
            $logger = new MonologLogger($loggerName);
            // skip a frame so that helper functions like info, warning, error etc report
            // the caller filename and line no.
            $logger->pushProcessor(new IntrospectionProcessor(skipStackFramesCount: 1));
            $logger->pushHandler(new StreamHandler('php://stdout', \Monolog\Level::Debug));
            self::$instances[$loggerName] = $inst;
        }

        $inst = self::$instances[$loggerName];

        return $inst->logger;
    }

    public function __wakeup(): void
    {
        throw new \Exception('cannot unserialize a singleton.');
    }

    /**
     * @param string|\Stringable $message
     */
    public static function error(mixed $message, mixed ...$context): void
    {
        self::getInstance()->error($message, $context);
    }

    /**
     * @param string|\Stringable $message
     */
    public static function warning(mixed $message, mixed ...$context): void
    {
        self::getInstance()->warning($message, $context);
    }

    /**
     * @param string|\Stringable $message
     */
    public static function info(mixed $message, mixed ...$context): void
    {
        self::getInstance()->info($message, $context);
    }

    /**
     * @param string|\Stringable $message
     */
    public static function notice(mixed $message, mixed ...$context): void
    {
        self::getInstance()->notice($message, $context);
    }

    /**
     * @param string|\Stringable $message
     */
    public static function debug(mixed $message, mixed ...$context): void
    {
        self::getInstance()->debug($message, $context);
    }
}
