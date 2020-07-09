<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model;

class TestData
{
    /**
     * Get constant 1 data.
     *
     * @return mixed[]
     */
    public static function getConstant1Data(): array
    {
        return [
            'title' => 'Constants for numbers.',
            'name' => 'code',
            'value' => 'number',
            'prefix' => 'NUM',
            'suffix' => 'S',
            'replace' => [
                'SE' => '>>',
                'ON' => '<<',
            ],
        ];
    }

    /**
     * Get constant 2 data.
     *
     * @return mixed[]
     */
    public static function getConstant2Data(): array
    {
        return [
            'title' => 'Constants for strings.',
            'name' => 'code',
            'value' => 'string',
            'prefix' => 'STR',
            'suffix' => 'S',
            'replace' => [
                'SE' => '>>',
                'ON' => '<<',
            ],
        ];
    }

    /**
     * Get constants data.
     *
     * @return mixed[]
     */
    public static function getConstantsData(): array
    {
        return [
            'constants' => [
                self::getConstant1Data(),
                self::getConstant2Data(),
            ],
        ];
    }

    /**
     * Get full data.
     *
     * @return mixed[]
     */
    public static function getFullData(): array
    {
        return array_merge(
            [
                'created_at' => 'created_at_test',
                'updated_at' => 'updated_at_test',
                'date_format' => 'U',
                'fillable' => ['code', 'number', 'string'],
                'guarded' => ['code', 'number', 'string'],
                'readonly' => ['code']
            ],
            self::getConstantsData()
        );
    }

    /**
     * Get.
     *
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public static function get(string $key, $default = null)
    {
        if (trim($key) === '') {
            return $default;
        }

        $data = self::getFullData();

        $segments = explode('.', $key);
        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $data)) {
                return $default;
            }

            $data = &$data[$segment];
        }

        return $data;
    }

    /**
     * Get config.
     *
     * @param mixed[] $change
     * @param string[] $remove
     * @return mixed[]
     */
    public static function getConfig(array $change = [], array $remove = []): array
    {
        $data = require __DIR__ . '/Files/lmodel-config.php';

        // Change.
        if (count($change) > 0) {
            foreach ($change as $key => $value) {
                $data[$key] = $value;
            }
        }

        // Remove.
        if (count($remove) > 0) {
            foreach ($remove as $key) {
                if (array_key_exists($key, $data)) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }
}
