<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Debug\Benchmarker;
use Spiral\Debug\BenchmarkerInterface;
use Spiral\Debug\Traits\BenchmarkTrait;

class TraitsTest extends TestCase
{
    use BenchmarkTrait;

    public function testBenchmarkTrait()
    {
        $b = $this->benchmark('test');
        $this->assertSame(self::class, $b->getCaller());
        $this->assertSame('test', $b->getEvent());
    }

    public function testBenchmarkTraitInScope()
    {
        $c = new Container();
        $c->bindSingleton(BenchmarkerInterface::class, new Benchmarker());

        $b = ContainerScope::runScope($c, function () {
            $b = $this->benchmark('test', ['key' => 'value']);

            return $b;
        });

        /**
         * @var Benchmarker             $bench
         * @var \Spiral\Debug\Benchmark $b
         */
        $bench = $c->get(BenchmarkerInterface::class);

        $this->assertSame(self::class, $b->getCaller());
        $this->assertSame('test', $b->getEvent());
        $this->assertSame(['key' => 'value'], $b->getContext());

        $this->assertCount(1, $bench->getRecords());
        $this->assertTrue(in_array($b, $bench->getRecords()));
    }
}