<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Debug\Benchmarker;

class BenchmarkTest extends TestCase
{
    public function testRecord()
    {
        $b = new Benchmarker();
        $this->assertCount(0, $b->getRecords());

        $r = $b->record('test', 'method', ['magic']);

        $this->assertSame('test', $r->getCaller());
        $this->assertSame('method', $r->getEvent());
        $this->assertSame(['magic'], $r->getContext());
        $this->assertTrue($r->getStart() <= microtime(true));

        $this->assertFalse($r->isComplete());
        $this->assertNull($r->getFinish());
        $this->assertNull($r->getElapsed());

        $this->assertCount(1, $b->getRecords());
        $this->assertSame($r, $b->getRecords()[0]);
    }

    public function testComplete()
    {
        $b = new Benchmarker();
        $r = $b->record('test', 'method', ['magic']);
        $r->complete();

        $this->assertTrue($r->isComplete());
        $this->assertNotNull($r->getFinish());
        $this->assertNotNull($r->getElapsed());
        $this->assertNotNull($r->getFinish());
        $this->assertTrue($r->getFinish() <= microtime(true));
    }
}