<?php

declare(strict_types=1);

namespace Tests\CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Helpers\Obj;
use CoRex\Laravel\Model\Exceptions\ConfigException;
use CoRex\Laravel\Model\Helpers\Definitions\PackageDefinition;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class PackageDefinitionTest extends TestCase
{
    /** @var mixed[] */
    private $data;

    /** @var PackageDefinition */
    private $definition;

    /**
     * Test constructor.
     *
     * @throws ReflectionException
     */
    public function testConstructor(): void
    {
        $this->assertSame(
            $this->data,
            Obj::getProperty('data', $this->definition)
        );
    }

    /**
     * Test is valid.
     */
    public function testIsValid(): void
    {
        $this->assertFalse((new PackageDefinition([]))->isValid());
        $this->assertTrue((new PackageDefinition(['something' => 'something']))->isValid());
    }

    /**
     * Test get model filename.
     *
     * @throws ConfigException
     */
    public function testGetModelFilename(): void
    {
        $this->assertSame(
            dirname(__DIR__, 3) . '/src/Models/Unknown.php',
            $this->definition->getModelFilename('unknown')
        );
    }

    /**
     * Test get model namespace.
     *
     * @throws ConfigException
     */
    public function testGetModelNamespace(): void
    {
        $this->assertSame('CoRex\Laravel\Model\Models', $this->definition->getModelNamespace());
    }

    /**
     * Test get model namespace no "autoload.psr4".
     *
     * @throws ConfigException
     */
    public function testGetModelNamespaceNoAutoloadPsr4(): void
    {
        $composerJsonPath = dirname(__DIR__, 2) . '/Files';
        $definition = new PackageDefinition(
            [
                'package' => $composerJsonPath,
                'relative' => 'src/Models'
            ]
        );

        $message = 'Could not find autoload.psr-4 in "' . $composerJsonPath . '/composer.json' . '".';
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage($message);
        $this->assertSame(
            $composerJsonPath . '/composer.json',
            $definition->getModelNamespace()
        );
    }

    /**
     * Get get model class.
     *
     * @throws ConfigException
     */
    public function testGetModelClass(): void
    {
        $this->assertSame('CoRex\Laravel\Model\Models\Unknown', $this->definition->getModelClass('unknown'));
    }

    /**
     * Test match.
     */
    public function testMatch(): void
    {
        $this->assertFalse($this->definition->match('unknown'));
        $this->assertTrue($this->definition->match('test'));
    }

    /**
     * Test get relative path.
     *
     * @throws ReflectionException
     */
    public function testGetRelativePath(): void
    {
        $this->assertSame(
            'src/Models',
            Obj::callMethod('getRelativePath', $this->definition)
        );
    }

    /**
     * Test get relative path not found.
     *
     * @throws ReflectionException
     */
    public function testGetRelativePathNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Package relative path {relative} is not specified.');
        $definition = new PackageDefinition([]);
        Obj::callMethod('getRelativePath', $definition);
    }

    /**
     * Test get absolute path.
     *
     * @throws ReflectionException
     */
    public function testGetAbsolutePath(): void
    {
        $this->assertSame(
            dirname(__DIR__, 3),
            Obj::callMethod('getAbsolutePath', $this->definition)
        );
    }

    /**
     * Test get absolute path not found.
     *
     * @throws ReflectionException
     */
    public function testGetAbsolutePathNotFound(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Package absolute path {package} is not specified.');
        $definition = new PackageDefinition([]);
        Obj::callMethod('getAbsolutePath', $definition);
    }

    /**
     * Test get absolute path does not exist.
     *
     * @throws ReflectionException
     */
    public function testGetAbsolutePathDoesNotExist(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Path "unknown.path" does not exist.');
        $definition = new PackageDefinition(
            [
                'package' => 'unknown.path',
                'relative' => 'src/Models'
            ]
        );
        Obj::callMethod('getAbsolutePath', $definition);
    }

    /**
     * Test get absolute path composer.json not found.
     *
     * @throws ReflectionException
     */
    public function testGetAbsolutePathComposerJsonNotFound(): void
    {
        $composerJsonPath = dirname(__DIR__, 2) . '/Files/Testbench';
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('"composer.json" not found in "' . $composerJsonPath . '".');
        $definition = new PackageDefinition(
            [
                'package' => $composerJsonPath,
                'relative' => 'src/Models'
            ]
        );
        Obj::callMethod('getAbsolutePath', $definition);
    }

    /**
     * Test get.
     *
     * @throws ReflectionException
     */
    public function testGet(): void
    {
        $this->assertSame('unknown', Obj::callMethod('get', $this->definition, ['my.key', 'unknown']));
    }

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'package' => dirname(__DIR__, 3),
            'relative' => 'src/Models',
            'patterns' => [
                '^test'
            ]
        ];

        $this->definition = new PackageDefinition($this->data);
    }
}
