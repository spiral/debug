<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\BootloadManager;
use Spiral\Core\Container;
use Spiral\Core\ContainerScope;
use Spiral\Debug\Bootloaders\DebugBootloader;

class FunctionsTest extends TestCase
{
    public function testDumpNoScope()
    {
        $this->assertContains("100", dump(100, 1));
    }

    public function testDumpWithScope()
    {
        $container = new Container();
        $bootloader = new BootloadManager($container);
        $bootloader->bootload([DebugBootloader::class]);

        ContainerScope::runScope($container, function () {
            $this->assertContains("100", dump(100, 1));
        });
    }
}