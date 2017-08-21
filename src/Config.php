<?php

namespace CoRex\Laravel\Model;

class Config
{
    private static $overrideValues;

    /**
     * Validate.
     *
     * @throws \Exception
     */
    public static function validate()
    {
        // Validate path.
        if (self::value('path') === null) {
            throw new \Exception('[' . self::fullPath('path') . '] not set');
        }

        // Validate namespace.
        if (self::value('namespace') === null) {
            throw new \Exception('[' . self::fullPath('namespace') . '] not set');
        }

        // Validate addConnection.
        if (self::value('addConnection') === null) {

            throw new \Exception('[' . self::fullPath('addConnection') . '] not set');
        }
    }

    /**
     * Get const settings.
     *
     * @param string $connection
     * @param string $table
     * @return array
     */
    public static function constSettings($connection, $table)
    {
        return self::value('const.' . $connection . '.' . $table);
    }

    /**
     * Value.
     *
     * @param string $path
     * @param mixed $defaultValue Default null.
     * @return mixed
     */
    public static function value($path, $defaultValue = null)
    {
        if (!is_array(self::$overrideValues)) {
            self::$overrideValues = [];
        }
        $path = self::fullPath($path);
        if (isset(self::$overrideValues[$path])) {
            $value = self::$overrideValues[$path];
        } else {
            $value = config($path);
        }
        if ($value === null) {
            return $defaultValue;
        }
        return $value;
    }

    /**
     * Clear overrides.
     */
    public static function clearOverrides()
    {
        self::$overrideValues = [];
    }

    /**
     * Set path override.
     *
     * @param string $path
     */
    public static function setPathOverride($path)
    {
        self::setValue('path', $path);
    }

    /**
     * Set namespace override.
     *
     * @param string $namespace
     */
    public static function setNamespaceOverride($namespace)
    {
        self::setValue('namespace', $namespace);
    }

    /**
     * Set addConnection override.
     *
     * @param boolean $addConnection
     */
    public static function setAddConnection($addConnection)
    {
        self::setValue('addConnection', $addConnection);
    }

    /**
     * Set extends override.
     *
     * @param string $extends
     */
    public static function setExtendsOverride($extends)
    {
        self::setValue('extends', $extends);
    }

    /**
     * Set indent override.
     *
     * @param string $indent
     */
    public static function setIndentOverride($indent)
    {
        self::setValue('indent', $indent);
    }

    /**
     * Set length override.
     *
     * @param integer $length
     */
    public static function setLengthOverride($length)
    {
        self::setValue('length', $length);
    }

    /**
     * Set value.
     *
     * @param string $path
     * @param mixed $value
     */
    private static function setValue($path, $value)
    {
        $path = 'corex.' . self::packageName() . '.' . $path;
        self::$overrideValues[$path] = $value;
    }

    /**
     * Full path.
     *
     * @param string $path
     * @return string
     */
    private static function fullPath($path)
    {
        return 'corex.' . self::packageName() . '.' . $path;
    }

    /**
     * Pacakge name.
     *
     * @return string
     */
    private static function packageName()
    {
        return basename(dirname(__DIR__));
    }
}