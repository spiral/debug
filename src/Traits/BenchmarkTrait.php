<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Traits;

use Spiral\Core\ContainerScope;
use Spiral\Debug\Benchmark;
use Spiral\Debug\BenchmarkerInterface;

/**
 * Provides access to Benchmarker over global container scope.
 */
trait BenchmarkTrait
{
    /**
     * Creates new benchmark record associated with current object.
     *
     * @param string $event   Event name or set of names.
     * @param string $context Record context (if any).
     *
     * @return Benchmark
     */
    private function benchmark(string $event, $context = null): Benchmark
    {
        $container = ContainerScope::getContainer();
        if (empty($container) || !$container->has(BenchmarkerInterface::class)) {
            return new Benchmark(
                get_class($this),
                $event,
                $context
            );
        }

        return $container->get(BenchmarkerInterface::class)->record(
            get_class($this),
            $event,
            $context
        );
    }
}