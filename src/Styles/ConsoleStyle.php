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
    private $stream;

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

    public function __construct($stream = STDOUT)
    {

    }

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
     * Colorization is disabled if not supported by the stream:
     *
     *  -  Windows without Ansicon, ConEmu or Babun
     *  -  non tty consoles
     *
     * @return bool true if the stream supports colorization, false otherwise
     *
     * @link https://github.com/symfony/Console/blob/master/Output/StreamOutput.php#L94
     * @codeCoverageIgnore
     */
    public function isSupported(): bool
    {
        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }
        if (\DIRECTORY_SEPARATOR === '\\') {
            return (\function_exists('sapi_windows_vt100_support')
                    && @sapi_windows_vt100_support($this->stream))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }
        if (\function_exists('stream_isatty')) {
            return @stream_isatty($this->stream);
        }
        if (\function_exists('posix_isatty')) {
            return @posix_isatty($this->stream);
        }

        $stat = @fstat($this->stream);

        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }

    /**
     * Returns true if the stream supports colorization.
     *
     * Reference: Composer\XdebugHandler\Process::supportsColor
     * https://github.com/composer/xdebug-handler
     *
     * @param mixed $stream A CLI output stream
     *
     * @return bool
     */
    private function hasColorSupport($stream)
    {
        if (!\is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            return false;
        }

        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            return (\function_exists('sapi_windows_vt100_support')
                    && @sapi_windows_vt100_support($stream))
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }
        if (\function_exists('stream_isatty')) {
            return @stream_isatty($stream);
        }
        if (\function_exists('posix_isatty')) {
            return @posix_isatty($stream);
        }
        $stat = @fstat($stream);
        // Check if formatted mode is S_IFCHR
        return $stat ? 0020000 === ($stat['mode'] & 0170000) : false;
    }

    /**
     * Returns true if the Windows terminal supports true color.
     *
     * Note that this does not check an output stream, but relies on environment
     * variables from known implementations, or a PHP and Windows version that
     * supports true color.
     *
     * @return bool
     */
    private function isWindowsTrueColor()
    {
        $result = 183 <= getenv('ANSICON_VER')
            || 'ON' === getenv('ConEmuANSI')
            || 'xterm' === getenv('TERM')
            || 'Hyper' === getenv('TERM_PROGRAM');
        if (!$result && \PHP_VERSION_ID >= 70200) {
            $version = sprintf(
                '%s.%s.%s',
                PHP_WINDOWS_VERSION_MAJOR,
                PHP_WINDOWS_VERSION_MINOR,
                PHP_WINDOWS_VERSION_BUILD
            );
            $result = $version >= '10.0.15063';
        }
        return $result;
    }
}