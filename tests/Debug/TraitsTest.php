<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Debug\Benchmarker;
use Spiral\Debug\Bootloaders\DebugBootloader;
use Spiral\Debug\LogsInterface;
use Spiral\Debug\Traits\BenchmarkTrait;
use Spiral\Debug\Traits\LoggerTrait;

class TraitsTest extends TestCase
{
    use LoggerTrait, BenchmarkTrait;

    public function testLoggerTrait()
    {
        $this->assertInstanceOf(NullLogger::class, $this->getLogger());
        $this->logger = null;

        $logger = new NullLogger();
        $logs = $this->createMock(LogsInterface::class);
        $logs->method('getLogger')->with(self::class)->willReturn($logger);

        $c = new Container();
        $c->bind(LogsInterface::class, $logs);

        ContainerScope::runScope($c, function () use ($logger) {
            $this->assertSame($logger, $this->getLogger());
        });

        $logger2 = new NullLogger();
        $this->setLogger($logger2);
        $this->assertSame($logger2, $this->getLogger());
    }


    public function testBenchmarkTrait()
    {
        $b = $this->benchmark('test');
        $this->assertSame(self::class, $b->getCaller());
        $this->assertSame('test', $b->getEvent());
    }

    public function testBenchmarkTraitInScope()
    {
        $c = new Container();

        $b = new BootloadManager($c);
        $b->bootload([DebugBootloader::class]);

        $b = ContainerScope::runScope($c, function () {
            $b = $this->benchmark('test', ['key' => 'value']);

            return $b;
        });

        /**
         * @var Benchmarker             $bench
         * @var \Spiral\Debug\Benchmark $b
         */
        $bench = $c->get(Benchmarker::class);

        $this->assertSame(self::class, $b->getCaller());
        $this->assertSame('test', $b->getEvent());
        $this->assertSame(['key' => 'value'], $b->getContext());

        $this->assertCount(1, $bench->getRecords());
        $this->assertTrue(in_array($b, $bench->getRecords()));
    }
}