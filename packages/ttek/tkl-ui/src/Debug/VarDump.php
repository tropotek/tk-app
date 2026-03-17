<?php


namespace Tk\Debug;


/**
 * vd(), vdd() helper functions
 * Improved var dumper functions, helps visualize objects, arrays and var types better
 */
class VarDump
{
    protected static mixed $_instance = null;
    protected string $basePath = '';


    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
    }

    public static function instance(string $basePath = ''): self
    {
        if (is_null(static::$_instance)) {
            if (!$basePath) $basePath = dirname(__DIR__, 2);
            static::$_instance = new self($basePath);
        }
        return static::$_instance;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;
        return $this;
    }

    public function makeDump(array $args, bool $showTrace = false): string
    {
        $str = $this->argsToString($args);
        if ($showTrace) {
            $str .= "\n" . self::getBacktrace(4, $this->basePath) . "\n";
        }
        return $str;
    }

    public function argsToString(array $args): string
    {
        $output = '';
        foreach ($args as $var) {
            $output .= self::varToString($var) . "\n";
        }
        return $output;
    }

    /**
     * return the types of the argument array
     */
    public function getTypeArray(array $args): array
    {
        $arr = [];
        foreach ($args as $a) {
            $type = gettype($a);
            if ($type == 'object') {
                $type = str_replace("\0", '', strval(get_class($a)));
            }
            $arr[] = $type;
        }
        return $arr;
    }

    /**
     * return a var dump string from an array of arguments
     */
    public static function varToString(mixed $var, int $depth = 6, int $nest = 0): string
    {
        $pad = str_repeat('  ', $nest * 2 + 1);

        //$type = 'native';
        $str = $var;

        if ($var === null) {
            $str = '{NULL}';
        } else if (is_bool($var)) {
            //$type = 'Boolean';
            $str = $var ? '{true}' : '{false}';
        } else if (is_string($var)) {
            //$type = 'String';
            $str = str_replace("\0", '|', $var);
            $str = "'{$str}'";
        } else if (is_resource($var)) {
            //$type = 'Resource';
            $str = get_resource_type($var);
        } else if (is_array($var)) {
            $type = sprintf('Array[%s]', count($var));
            $a = array();
            if ($nest >= $depth) {
                $str = $type;
            } else {
                foreach ($var as $k => $v) {
                    $a[] = sprintf("%s[%s] => %s\n", $pad, $k, self::varToString($v, $depth, $nest + 1));
                }
                $str = sprintf("%s \n%s(\n%s\n%s)", $type, substr($pad, 0, -2), implode('', $a), substr($pad, 0, -2));
            }
        } else if (is_object($var)) {
            $class = str_replace("\0", '', get_class($var));
            $type = '{' . $class . '} Object';
            if ($nest >= $depth) {
                $str = $type;
            } else {
                $a = array();
                foreach ((array)$var as $k => $v) {
                    $k = str_replace($class, '*', $k);
                    $a[] = sprintf("%s[%s] => %s", $pad, $k, self::varToString($v, $depth, $nest + 1));
                }
                $str = sprintf("%s \n%s{\n%s\n%s}", $type, substr($pad, 0, -2), implode("\n", $a), substr($pad, 0, -2));
            }
        }
        return $str;
    }

    /**
     * Get the backtrace dump as a string
     */
    static function getBacktrace(int $skip = 1, string $sitePath = ''): string
    {
        $stackTraceArray = debug_backtrace();
        for ($i = 0; $i < $skip && $i < count($stackTraceArray); $i++) {
            array_shift($stackTraceArray);
        }
        return self::traceToString($stackTraceArray, $sitePath);
    }

    /**
     * Take a stack trace array from \Exception::getTrace or debug_backtrace()
     * and convert it to a string
     */
    static function traceToString(array $stackTraceArray, string $sitePath = ''): string
    {
        $str = '';
        foreach ($stackTraceArray as $i => $t) {
            $type = '';
            if (isset($t['type'])) {
                $type = $t['type'];
            }
            $class = '';
            if (isset($t['class'])) {
                $class = $t['class'];
            }
            $file = '';
            if (isset($t['file'])) {
                $file = $t['file'];
                if ($sitePath) {    // Make the path relative if sitePath exists
                    $file = str_replace($sitePath, '', $file);
                }
            }
            $line = '';
            if (isset($t['line'])) {
                $line = $t['line'];
            }
            $function = '';
            if (isset($t['function'])) {
                $function = $t['function'];
            }
            $args = '()';
            $astr = '';
            if (isset($t['args'])) {
                foreach ($t['args'] as $o) {
                    if (is_object($o)) {
                        $o = str_replace("\0", '', get_class($o));
                    }
                    if (is_array($o)) {
                        $o = 'Array['.count($o).']';
                    }
                    if ($o === null) $o = '{null}';
                    if (is_string($o) || $o == '') $o = "'" . str_replace(["\n", "\r"], ' ', substr((string)$o, 0, 32)) . "'";
                    $astr .= $o . ', ';
                }
            }
            if ($astr) {
                $args = '(' . substr($astr, 0, -2) . ')';
            }

            $str .= sprintf("[%s] %s(%s): %s%s%s%s \n", $i, $file, $line, $class, $type, $function, $args);
        }
        return trim($str);
    }

    public static function dumpTraceLine(int $dumpLine = 1, bool $showClass = false, bool $showFunction = false): string
    {
        $line = debug_backtrace();
        $line = $line[$dumpLine];

        $class = '';
        if ($showClass && !empty($line['object'])) {
            $class = ': ' . str_replace("\0", '', get_class($line['object']));
        }

        if ($showFunction && !empty($line['function'])) {
            $class .= '::' . $line['function'] . '()';
        }

        //$path = str_replace(base_path(), '', $line['file'] ?? '');
        $path = str_replace(app_path(), '', $line['file'] ?? '');
        return sprintf('%s [%s]%s', $path, $line['line'] ?? 0, $class);
    }

}
