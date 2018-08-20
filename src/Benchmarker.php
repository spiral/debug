<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

class Benchmarker
{
    /** @var Benchmark[] */
    private $records = [];

    /**
     * Create new benchmark record.
     *
     * @param string $caller
     * @param string $event
     * @param mixed  $context
     * @return Benchmark
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