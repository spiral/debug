<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Renderer;

use Spiral\Debug\RendererInterface;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * Container element used to inject dump into, usually pre elemnt with some styling.
     *
     * @var string
     */
    protected $body = '{body}';

    /**
     * Default indent string.
     *
     * @var string
     */
    protected $indent = '    ';

    /**
     * @inheritdoc
     */
    public function wrapContent(string $body): string
    {
        return $this->interpolate($this->body, compact('body'));
    }

    /**
     * @inheritdoc
     */
    public function indent(int $level): string
    {
        if ($level == 0) {
            return '';
        }

        return $this->apply(str_repeat($this->indent, $level), 'indent');
    }

    /**
     * Replaces {placeholders} with given values.
     *
     * @param string $string
     * @param array  $values
     * @param string $prefix
     * @param string $postfix
     * @return string
     */
    protected function interpolate(
        string $string,
        array $values,
        string $prefix = '{',
        string $postfix = '}'
    ): string {
        $replaces = [];
        foreach ($values as $key => $value) {
            $value = (is_array($value) || $value instanceof \Closure) ? '' : $value;

            try {
                //Object as string
                $value = is_object($value) ? (string)$value : $value;
            } catch (\Exception $e) {
                $value = '';
            }

            $replaces[$prefix . $key . $postfix] = $value;
        }

        return strtr($string, $replaces);
    }
}