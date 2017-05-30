<?php

namespace CoRex\Laravel\Model;

class Config
{
    /**
     * Validate.
     *
     * @throws \Exception
     */
    public static function validate()
    {
        // Validate path.
        if (empty(self::value('path'))) {
            throw new \Exception('[' . self::fullPath('path') . '] not set');
        }

        // Validate namespace.
        if (empty(self::value('namespace'))) {
            throw new \Exception('[' . self::fullPath('namespace') . '] not set');
        }

        // Validate addConnection.
        if (empty(self::value('addConnection'))) {
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
        $value = config(self::fullPath($path));
        if (!empty($value)) {
            return $value;
        }
        return $defaultValue;
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