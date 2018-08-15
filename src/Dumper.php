<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Debug;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Spiral\Debug\Styles\DefaultStyle;

/**
 * Styles exports the content of the given variable, array or object into human friendly form.
 */
class Dumper implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Options for dump() function to specify output.
     */
    const OUTPUT_ECHO = 0;
    const OUTPUT_RETURN = 1;
    const OUTPUT_LOG = 2;

    /**
     * Deepest level to be dumped.
     *
     * @var int
     */
    private $maxLevel = 10;

    /**
     * @invisible
     *
     * @var Style
     */
    private $style = null;

    public $xxx = [
        'asdas'  => 123213,
        "asdasd" => 'test'
    ];

    /**
     * @param int             $maxLevel Defines how deep dumper should go while dumping the arrays or objects.
     * @param Style           $style    Light styler to be used by default.
     * @param LoggerInterface $logger
     */
    public function __construct(int $maxLevel = 10, Style $style = null, LoggerInterface $logger = null)
    {
        $this->maxLevel = $maxLevel;
        $this->style = $style ?? new DefaultStyle();

        if (!empty($logger)) {
            $this->setLogger($logger);
        }
    }

    /**
     * Set dump styler.
     *
     * @param Style $style
     * @return self
     */
    public function setStyle(Style $style): Dumper
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Return new copy of dumper with given style.
     *
     * @param Style $style
     * @return self
     */
    public function withStyle(Style $style): Dumper
    {
        $dumper = clone $this;
        $dumper->style = $style;

        return $dumper;
    }

    /**
     * Dump specified value. Styles will automatically detect CLI mode in OUTPUT_ECHO mode.
     *
     * @param mixed $value
     * @param int   $output
     *
     * @return string
     */
    public function dump($value, int $output = self::OUTPUT_ECHO): string
    {
        //todo: cleanup
        switch ($output) {
            case self::OUTPUT_ECHO:
                echo $this->style->wrapBody($this->dumpValue($value, '', 0));
                break;

            case self::OUTPUT_LOG:
                if (!empty($this->logger)) {
                    $this->logger->debug($this->dump($value, self::OUTPUT_RETURN));
                }
                break;

            case self::OUTPUT_RETURN:
                return $this->style->wrapBody($this->dumpValue($value, '', 0));
        }

        //Nothing to return
        return '';
    }

    /**
     * Variable dumper. This is the oldest spiral function originally written in 2007. :).
     *
     * @param mixed  $value
     * @param string $name       Variable name, internal.
     * @param int    $level      Dumping level, internal.
     * @param bool   $hideHeader Hide array/object header, internal.
     *
     * @return string
     */
    private function dumpValue($value, string $name = '', int $level = 0, bool $hideHeader = false): string
    {
        //Any dump starts with initial indent (level based)
        $indent = $this->style->indent($level);

        if (!$hideHeader && !empty($name)) {
            //Showing element name (if any provided)
            $header = $indent . $this->style->apply($name, 'name');

            //Showing equal sing
            $header .= $this->style->apply(' = ', 'syntax', '=');
        } else {
            $header = $indent;
        }

        if ($level > $this->maxLevel) {
            //Styles is not reference based, we can't dump too deep values
            return $indent . $this->style->apply('-too deep-', 'maxLevel') . "\n";
        }

        $type = strtolower(gettype($value));

        if ($type == 'array') {
            return $header . $this->dumpArray($value, $level, $hideHeader);
        }

        if ($type == 'object') {
            return $header . $this->dumpObject($value, $level, $hideHeader);
        }

        if ($type == 'resource') {
            //No need to dump resource value
            $element = get_resource_type($value) . ' resource ';

            return $header . $this->style->apply($element, 'type', 'resource') . "\n";
        }

        //Value length
        $length = strlen($value);

        //Including type size
        $header .= $this->style->apply("{$type}({$length})", 'type', $type);

        $element = null;
        switch ($type) {
            case 'string':
                $element = htmlspecialchars($value);
                break;

            case 'boolean':
                $element = ($value ? 'true' : 'false');
                break;

            default:
                if ($value !== null) {
                    //Not showing null value, type is enough
                    $element = var_export($value, true);
                }
        }

        //Including value
        return $header . ' ' . $this->style->apply($element, 'value', $type) . "\n";
    }

    /**
     * @param array $array
     * @param int   $level
     * @param bool  $hideHeader
     *
     * @return string
     */
    private function dumpArray(array $array, int $level, bool $hideHeader = false): string
    {
        $indent = $this->style->indent($level);

        if (!$hideHeader) {
            $count = count($array);

            //Array size and scope
            $output = $this->style->apply("array({$count})", 'type', 'array') . "\n";
            $output .= $indent . $this->style->apply('[', 'syntax', '[') . "\n";
        } else {
            $output = '';
        }

        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                if (is_string($key)) {
                    $key = htmlspecialchars($key);
                }

                $key = "'{$key}'";
            }

            $output .= $this->dumpValue($value, "[{$key}]", $level + 1);
        }

        if (!$hideHeader) {
            //Closing array scope
            $output .= $indent . $this->style->apply(']', 'syntax', ']') . "\n";
        }

        return $output;
    }

    /**
     * @param object $o
     * @param int    $level
     * @param bool   $hideHeader
     * @param string $class
     *
     * @return string
     */
    private function dumpObject(object $o, int $level, bool $hideHeader = false, string $class = ''): string
    {
        $indent = $this->style->indent($level);

        if (!$hideHeader) {
            $type = ($class ?: get_class($o)) . ' object ';

            $header = $this->style->apply($type, 'type', 'object') . "\n";
            $header .= $indent . $this->style->apply('(', 'syntax', '(') . "\n";
        } else {
            $header = '';
        }

        //Let's use method specifically created for dumping
        if (method_exists($o, '__debugInfo') || $o instanceof \Closure) {
            if ($o instanceof \Closure) {
                $debugInfo = $this->describeClosure($o);
            } else {
                $debugInfo = $o->__debugInfo();
            }

            if (is_array($debugInfo)) {
                //Pretty view
                $debugInfo = (object)$debugInfo;
            }

            if (is_object($debugInfo)) {
                //We are not including syntax elements here
                return $this->dumpObject($debugInfo, $level, false, get_class($o));
            }

            return $header
                . $this->dumpValue($debugInfo, '', $level + (is_scalar($o)), true)
                . $indent . $this->style->apply(')', 'syntax', ')') . "\n";
        }

        $refection = new \ReflectionObject($o);

        $output = '';
        foreach ($refection->getProperties() as $property) {
            $output .= $this->dumpProperty($o, $property, $level);
        }

        //Header, content, footer
        return $header . $output . $indent . $this->style->apply(')', 'syntax', ')') . "\n";
    }

    /**
     * @param object              $o
     * @param \ReflectionProperty $p
     * @param int                 $level
     *
     * @return string
     */
    private function dumpProperty(object $o, \ReflectionProperty $p, int $level): string
    {
        if ($p->isStatic()) {
            return '';
        }

        if (
            !($o instanceof \stdClass)
            && strpos($p->getDocComment(), '@invisible') !== false
        ) {
            //Memory loop while reading doc comment for stdClass variables?
            //Report a PHP bug about treating comment INSIDE property declaration as doc comment.
            return '';
        }

        //Property access level
        $access = $this->getAccess($p);

        //To read private and protected properties
        $p->setAccessible(true);

        if ($o instanceof \stdClass) {
            $name = $this->style->apply($p->getName(), 'dynamic');
        } else {
            //Property name includes access level
            $name = $p->getName() . $this->style->apply(':' . $access, 'access', $access);
        }

        return $this->dumpValue($p->getValue($o), $name, $level + 1);
    }

    /**
     * Fetch information about the closure.
     *
     * @param \Closure $closure
     * @return array
     */
    private function describeClosure(\Closure $closure): array
    {
        try {
            $r = new \ReflectionFunction($closure);
        } catch (\ReflectionException $e) {
            return ['TODO'];
        }

        return [
            'name' => $r->getName() . " (lines {$r->getStartLine()}:{$r->getEndLine()})",
            'file' => $r->getFileName(),
            'this' => $r->getClosureThis()
        ];
    }

    /**
     * Property access level label.
     *
     * @param \ReflectionProperty $p
     *
     * @return string
     */
    private function getAccess(\ReflectionProperty $p): string
    {
        if ($p->isPrivate()) {
            return 'private';
        } elseif ($p->isProtected()) {
            return 'protected';
        }

        return 'public';
    }
}
