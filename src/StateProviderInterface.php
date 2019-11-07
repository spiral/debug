<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Debug;

use Spiral\Debug\Exception\StateException;

/**
 * Responsible of the description of application state for debug purposes.
 */
interface StateProviderInterface
{
    /**
     * Plugins will be called on a state sequentially.
     *
     * @param PluginInterface $plugin
     */
    public function registerPlugin(PluginInterface $plugin): void;

    /**
     * Return current application state if possible.
     *
     * @return State|null
     * @throws StateException
     */
    public function getState(): ?State;
}
