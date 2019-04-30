<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Debug;

/**
 * Provides benchmarking capabilities.
 */
interface BenchmarkerInterface
{
    /**
     * Create new benchmark record.
     *
     * @param string $caller
     * @param string $event
     * @param mixed  $context
     *
     * @return Benchmark
     */
    public function record(string $caller, string $event, $context = null): Benchmark;
}