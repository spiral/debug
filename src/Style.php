<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

abstract class Style
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
     * Inject dumped value into dump container.
     *
     * @param string $body
     *
     * @return string
     */
    public function wrapBody(string $body): string
    {
        return $this->interpolate($this->body, compact('body'));
    }

    /**
     * Set indent to line based on it's level.
     *
     * @param int $level
     * @return string
     */
    public function indent(int $level): string
    {
        if ($level == 0) {
            return '';
        }

        return $this->apply(str_repeat($this->indent, $level), 'indent');
    }

    /**
     * Stylize content using pre-defined style.
     *
     * @param string|null $element
     * @param string      $type
     * @param string      $context
     * @return string
     */
    abstract public function apply($element, string $type, string $context = ''): string;

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