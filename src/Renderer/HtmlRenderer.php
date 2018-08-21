<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Renderer;

/**
 * Default html style dumper.
 */
class HtmlRenderer extends AbstractRenderer
{
    /**
     * Container element used to inject dump into, usually pre elemnt with some styling.
     *
     * @var string
     */
    protected $body = '<pre style="background-color: white; font-family: monospace;">%s</pre>';

    /**
     * Every dumped element is wrapped using this pattern.
     *
     * @var string
     */
    protected $element = '<span style="%s;">%s</span>';

    /**
     * Default indent string.
     *
     * @var string
     */
    protected $indent = '&middot;    ';

    /**
     * Set of styles associated with different dumping properties.
     *
     * @var array
     */
    protected $styles = [
        'common'   => 'color: black',
        'name'     => 'color: black',
        'dynamic'  => 'color: purple;',
        'maxLevel' => 'color: #ff9900',
        'syntax'   => [
            'common' => 'color: #666',
            '['      => 'color: black',
            ']'      => 'color: black',
            '('      => 'color: black',
            ')'      => 'color: black',
        ],
        'value'    => [
            'string'  => 'color: green',
            'integer' => 'color: red',
            'double'  => 'color: red',
            'boolean' => 'color: purple; font-weight: bold;',
        ],
        'type'     => [
            'common'   => 'color: #666',
            'object'   => 'color: #333',
            'array'    => 'color: #333',
            'null'     => 'color: #666; font-weight: bold;',
            'resource' => 'color: #666; font-weight: bold;',
        ],
        'access'   => [
            'common'    => 'color: #666',
            'public'    => 'color: #8dc17d',
            'private'   => 'color: #c18c7d',
            'protected' => 'color: #7d95c1',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function apply($element, string $type, string $context = ''): string
    {
        if (!empty($style = $this->getStyle($type, $context))) {
            if (!empty($style = $this->getStyle($type, $context))) {
                return sprintf($this->element, $style, $element);
            }
        }

        return $element;
    }

    /**
     * Get valid stype based on type and context/.
     *
     * @param string $type
     * @param string $context
     *
     * @return string
     */
    private function getStyle(string $type, string $context): string
    {
        if (isset($this->styles[$type][$context])) {
            return $this->styles[$type][$context];
        }

        if (isset($this->styles[$type]['common'])) {
            return $this->styles[$type]['common'];
        }

        if (isset($this->styles[$type]) && is_string($this->styles[$type])) {
            return $this->styles[$type];
        }

        return $this->styles['common'];
    }
}
