<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Bootloaders;

use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Debug\Benchmarker;
use Spiral\Debug\BenchmarkerInterface;

class DebugBootloader extends Bootloader
{
    const BINDINGS = [
        BenchmarkerInterface::class => Benchmarker::class
    ];
}