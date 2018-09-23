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
use Spiral\Debug\Dumper;

class FunctionsTest extends TestCase
{
    public function testDumpNoScope()
    {
        $this->assertContains("100", dump(100, 1));
    }

    public function testDumpWithScope()
    {
        $container = new Container();
        $container->bindSingleton(Dumper::class, new Dumper());

        ContainerScope::runScope($container, function () {
            $this->assertContains("100", dump(100, 1));
        });
    }
}