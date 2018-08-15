<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug\Styles;

use Codedungeon\PHPCliColors\Color;
use Spiral\Debug\Style;

/**
 * Colorful styling for CLI dumps.
 */
class ConsoleStyle extends Style
{
    /**
     * Every dumped element is wrapped using this pattern.
     *
     * @var string
     */
    protected $element = '{style}{element}' . Color::RESET;

    /**
     * Set of styles associated with different dumping properties.
     *
     * @var array
     */
    protected $styles = [
        'common'   => Color::BOLD_WHITE,
        'name'     => Color::LIGHT_WHITE,
        'dynamic'  => Color::PURPLE,
        'maxLevel' => Color::RED,
        'syntax'   => [
            'common' => Color::WHITE,
            '['      => Color::BOLD_WHITE,
            ']'      => Color::BOLD_WHITE,
            '('      => Color::BOLD_WHITE,
            ')'      => Color::BOLD_WHITE,
        ],
        'value'    => [
            'string'  => Color::GREEN,
            'integer' => Color::LIGHT_CYAN,
            'double'  => Color::LIGHT_CYAN,
            'boolean' => Color::LIGHT_PURPLE,
        ],
        'type'     => [
            'common'   => Color::WHITE,
            'object'   => Color::LIGHT_BLUE,
            'null'     => Color::LIGHT_PURPLE,
            'resource' => Color::PURPLE,
        ],
        'access'   => Color::GRAY
    ];

    public function apply($element, string $type, string $context = ''): string
    {
        if (!empty($style = $this->getStyle($type, $context))) {
            return $this->interpolate(
                $this->element,
                compact('style', 'element')
            );
        }

        return $element;
    }

    /**
     * Get valid style based on type and context/.
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

    /**
     * Returns true if the stream supports colorization.
     *
     * @link https://github.com/symfony/Console/blob/master/Output/StreamOutput.php#L94
     * @codeCoverageIgnore
     * @return bool
     */
    private function isSupported(): bool
    {
        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            return (
                    \function_exists('sapi_windows_vt100_support')
                    && @sapi_windows_vt100_support(STDOUT)
                )
                || getenv('ANSICON') !== false
                || getenv('ConEmuANSI') == 'ON'
                || getenv('TERM') == 'xterm';
        }
        if (\function_exists('stream_isatty')) {
            return @stream_isatty(STDOUT);
        }

        if (\function_exists('posix_isatty')) {
            return @posix_isatty(STDOUT);
        }

        $stat = @fstat(STDOUT);

        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }
}