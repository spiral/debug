<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Traits;

use Spiral\Core\ContainerScope;
use Spiral\Core\Exceptions\ScopeException;
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
     *
     * @throws ScopeException
     */
    private function benchmark(string $event, $context = null): Benchmark
    {
        $container = ContainerScope::getContainer();
        if (empty($container) || !$container->has(BenchmarkerInterface::class)) {
            throw new ScopeException(
                'Unable to benchmark, `BenchmarkerInterface` binding is missing or container scope is not set'
            );
        }

        return $container->get(BenchmarkerInterface::class)->record(
            get_class($this),
            $event,
            $context
        );
    }
}