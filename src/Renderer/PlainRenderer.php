<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Renderer;

/**
 * No styles.
 */
class PlainRenderer extends AbstractRenderer
{
    /**
     * @inheritdoc
     */
    public function apply($element, string $type, string $context = ''): string
    {
        return (string)$element;
    }
}