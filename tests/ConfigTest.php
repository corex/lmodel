<?php

use CoRex\Laravel\Model\Config;
use Orchestra\Testbench\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Test validate.
     */
    public function testValidate()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
                'addConnection' => $check
            ]
        ]);
        Config::validate();
        $this->assertEquals($check, config('corex.lmodel.path'));
        $this->assertEquals($check, config('corex.lmodel.namespace'));
        $this->assertEquals($check, config('corex.lmodel.addConnection'));
    }

    /**
     * Test validate path not set.
     */
    public function testValidatePathNotSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'namespace' => $check,
                'addConnection' => $check
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.path] not set');
        Config::validate();
    }

    /**
     * Test validate namespace not set.
     */
    public function testValidateNamespaceNotSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'addConnection' => $check
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.namespace] not set');
        Config::validate();
    }

    /**
     * Test validate addConnection not set.
     */
    public function testValidateAddConnectionSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.addConnection] not set');
        Config::validate();
    }

    /**
     * Test const settings not set.
     */
    public function testConstSettingsNotSet()
    {
        $connection = md5(microtime()) . '1';
        $table = md5(microtime()) . '2';
        $constSettings = Config::constSettings($connection, $table);
        $this->assertNull($constSettings);
    }

    /**
     * Test const settings set.
     */
    public function testConstSettingsSet()
    {
        $connection = md5(microtime()) . '1';
        $table = md5(microtime()) . '2';
        $check1 = md5(microtime()) . '3';
        $check2 = md5(microtime()) . '4';
        $this->app['config']->set('corex', [
            'lmodel' => [
                'const' => [
                    $connection => [
                        $table => [
                            'id' => $check1,
                            'name' => $check2,
                        ]
                    ]
                ],
            ]
        ]);
        $constSettings = Config::constSettings($connection, $table);
        $this->assertEquals($check1, $constSettings['id']);
        $this->assertEquals($check2, $constSettings['name']);
    }

    /**
     * Test value not set.
     */
    public function testValueNotSet()
    {
        $check = md5(microtime());
        $this->assertNull(Config::value($check));
    }

    /**
     * Test value set.
     */
    public function testValueSet()
    {
        $check1 = md5(microtime()) . '1';
        $check2 = md5(microtime()) . '2';
        $this->app['config']->set('corex', [
            'lmodel' => [
                $check1 => $check2
            ]
        ]);
        $this->assertEquals($check2, Config::value($check1));
    }
}
