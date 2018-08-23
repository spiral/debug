<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

/**
 * Describes the env PHP script is running within.
 */
class System
{
    /**
     * Return true if PHP running in CLI mode.
     *
     * @codeCoverageIgnore
     * @return bool
     */
    public static function isCLI(): bool
    {
        if (!empty(getenv('RR'))) {
            // Do not treat RoadRunner as CLI.
            return false;
        }

        if (php_sapi_name() === 'cli') {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the STDOUT supports colorization.
     *
     * @codeCoverageIgnore
     * @param mixed $stream
     * @link https://github.com/symfony/Console/blob/master/Output/StreamOutput.php#L94
     * @return bool
     */
    public static function isColorsSupported($stream = STDOUT): bool
    {
        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            return (
                    \function_exists('sapi_windows_vt100_support')
                    && @sapi_windows_vt100_support($stream)
                )
                || getenv('ANSICON') !== false
                || getenv('ConEmuANSI') == 'ON'
                || getenv('TERM') == 'xterm';
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
}