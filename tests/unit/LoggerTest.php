<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Helpers\Logger;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests that Logger static methods don't throw and the singleton is stable.
 *
 * @internal
 */
final class LoggerTest extends CIUnitTestCase
{
    public function testInfoDoesNotThrow(): void
    {
        Logger::info('unit test info message');
        $this->addToAssertionCount(1);
    }

    public function testDebugDoesNotThrow(): void
    {
        Logger::debug('unit test debug message');
        $this->addToAssertionCount(1);
    }

    public function testWarningDoesNotThrow(): void
    {
        Logger::warning('unit test warning message');
        $this->addToAssertionCount(1);
    }

    public function testErrorDoesNotThrow(): void
    {
        Logger::error('unit test error message');
        $this->addToAssertionCount(1);
    }

    public function testNoticeDoesNotThrow(): void
    {
        Logger::notice('unit test notice message');
        $this->addToAssertionCount(1);
    }

    public function testLoggerAcceptsVariadicContext(): void
    {
        Logger::info('message with context', 'extra1', ['key' => 'value'], 42);
        $this->addToAssertionCount(1);
    }

    public function testLoggerIsSingleton(): void
    {
        // Calling multiple times should reuse the same instance without error
        Logger::debug('first call');
        Logger::debug('second call');
        Logger::debug('third call');
        $this->addToAssertionCount(1);
    }
}
