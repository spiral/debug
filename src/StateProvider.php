<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Debug;

final class StateProvider implements StateProviderInterface
{
    /** @var PluginInterface[] */
    private $plugins = [];

    /**
     * @inheritDoc
     */
    public function registerPlugin(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    /**
     * @inheritDoc
     */
    public function getState(): ?State
    {
        if ($this->plugins === []) {
            return null;
        }

        $state = new State();
        foreach ($this->plugins as $plugin) {
            $plugin->populate($state);
        }

        return $state;
    }
}
