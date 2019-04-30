<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Debug;

use Spiral\Core\Container\SingletonInterface;

final class Benchmarker implements BenchmarkerInterface, SingletonInterface
{
    /** @var Benchmark[] */
    private $records = [];

    /**
     * @inheritdoc
     */
    public function record(string $caller, string $event, $context = null): Benchmark
    {
        $record = new Benchmark($caller, $event, $context);
        $this->records[] = $record;

        return $record;
    }

    /**
     * Reset all benchmark records.
     */
    public function reset()
    {
        $this->records = [];
    }

    /**
     * Retrieve all benchmark records.
     *
     * @return Benchmark[]
     */
    public function getRecords(): array
    {
        return $this->records;
    }
}