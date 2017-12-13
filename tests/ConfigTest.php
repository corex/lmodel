<?php

use CoRex\Laravel\Model\Config;
use Orchestra\Testbench\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Test validate.
     * @throws Exception
     */
    public function testValidate()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
                'addConnection' => $check,
                'extends' => $check
            ]
        ]);
        Config::validate();
        $this->assertEquals($check, config('corex.lmodel.path'));
        $this->assertEquals($check, config('corex.lmodel.namespace'));
        $this->assertEquals($check, config('corex.lmodel.addConnection'));
        $this->assertEquals($check, config('corex.lmodel.extends'));
    }

    /**
     * Test validate path not set.
     * @throws Exception
     */
    public function testValidatePathNotSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'namespace' => $check,
                'addConnection' => $check,
                'extends' => $check
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.path] not set');
        Config::validate();
    }

    /**
     * Test validate namespace not set.
     * @throws Exception
     */
    public function testValidateNamespaceNotSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'addConnection' => $check,
                'extends' => $check
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.namespace] not set');
        Config::validate();
    }

    /**
     * Test validate addConnection not set.
     * @throws Exception
     */
    public function testValidateAddConnectionNotSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'namespace' => $check,
                'extends' => $check
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.addConnection] not set');
        Config::validate();
    }

    /**
     * Test validate addConnection not set.
     * @throws Exception
     */
    public function testValidateExtendsNotSet()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'addConnection' => $check,
                'namespace' => $check
            ]
        ]);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('[corex.lmodel.extends] not set');
        Config::validate();
    }

    /**
     * Test validate addConnection set false.
     */
    public function testValidateAddConnectionSetFalse()
    {
        $check = md5(microtime());
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check,
                'addConnection' => false,
            ]
        ]);
        $this->assertFalse(Config::value('addConnection'));
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
                            'name' => $check2
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

    /**
     * Test setPath.
     */
    public function testSetPath()
    {
        $check1 = md5(mt_rand(1, 100000));
        $check2 = md5(mt_rand(1, 100000));
        $this->app['config']->set('corex', [
            'lmodel' => [
                'path' => $check1
            ]
        ]);
        Config::setPathOverride($check2);
        $this->assertEquals($check2, Config::value('path'));
        Config::clearOverrides();
    }

    /**
     * Test setAddConnection.
     */
    public function testSetAddConnection()
    {
        $check1 = md5(mt_rand(1, 100000));
        $check2 = md5(mt_rand(1, 100000));
        $this->app['config']->set('corex', [
            'lmodel' => [
                'addConnection' => $check1
            ]
        ]);
        Config::setAddConnection($check2);
        $this->assertEquals($check2, Config::value('addConnection'));
        Config::clearOverrides();
    }

    /**
     * Set setNamespace()-
     */
    public function testSetNamespace()
    {
        $check1 = md5(mt_rand(1, 100000));
        $check2 = md5(mt_rand(1, 100000));
        $this->app['config']->set('corex', [
            'lmodel' => [
                'namespace' => $check1
            ]
        ]);
        Config::setNamespaceOverride($check2);
        $this->assertEquals($check2, Config::value('namespace'));
        Config::clearOverrides();
    }

    /**
     * Test setExtends().
     */
    public function testSetExtends()
    {
        $check1 = md5(mt_rand(1, 100000));
        $check2 = md5(mt_rand(1, 100000));
        $this->app['config']->set('corex', [
            'lmodel' => [
                'extends' => $check1
            ]
        ]);
        Config::setExtendsOverride($check2);
        $this->assertEquals($check2, Config::value('extends'));
        Config::clearOverrides();
    }

    /**
     * Test setIndent().
     */
    public function testSetIndent()
    {
        $check1 = md5(mt_rand(1, 100000));
        $check2 = md5(mt_rand(1, 100000));
        $this->app['config']->set('corex', [
            'lmodel' => [
                'indent' => $check1
            ]
        ]);
        Config::setIndentOverride($check2);
        $this->assertEquals($check2, Config::value('indent'));
        Config::clearOverrides();
    }

    /**
     * Test setLength().
     */
    public function testSetLength()
    {
        $check1 = md5(mt_rand(1, 100000));
        $check2 = md5(mt_rand(1, 100000));
        $this->app['config']->set('corex', [
            'lmodel' => [
                'length' => $check1
            ]
        ]);
        Config::setLengthOverride($check2);
        $this->assertEquals($check2, Config::value('length'));
        Config::clearOverrides();
    }
}
