<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Laravel\Model\Exceptions\ConfigException;
use Illuminate\Support\Str;

class PackageDefinition
{
    /** @var mixed[] */
    private $data;

    /**
     * ConstantDefinition constructor.
     *
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->data) > 0;
    }

    /**
     * Get model filename.
     *
     * @param string $table
     * @return string
     * @throws ConfigException
     */
    public function getModelFilename(string $table): string
    {
        $filename = $this->getAbsolutePath();
        $filename .= '/' . $this->getRelativePath();
        $filename .= '/' . Str::studly($table) . '.php';

        return $filename;
    }

    /**
     * Get model namespace.
     *
     * @return string
     * @throws ConfigException
     */
    public function getModelNamespace(): string
    {
        // Load "composer.json".
        $content = file_get_contents($this->getAbsolutePath() . '/composer.json');
        $composerJson = json_decode($content, true);

        // Validate "autoload".
        if (!isset($composerJson['autoload']['psr-4'])) {
            $message = sprintf('Could not find autoload.psr-4 in "%s/composer.json' . '".', $this->getAbsolutePath());
            throw new ConfigException($message);
        }

        // Resolve namespace.
        $packageNamespace = trim(key($composerJson['autoload']['psr-4']), '\\');
        $packageDirectory = trim(current($composerJson['autoload']['psr-4']), '/');
        $pathRelative = trim($this->get('relative'), '/');
        $namespaceRelative = trim(str_replace($packageDirectory, '', $pathRelative), '/');

        return $packageNamespace . '\\' . $namespaceRelative;
    }

    /**
     * Get model class.
     *
     * @param string $table
     * @return string
     * @throws ConfigException
     */
    public function getModelClass(string $table): string
    {
        return $this->getModelNamespace() . '\\' . Str::studly($table);
    }

    /**
     * Match.
     *
     * @param string $table
     * @return bool
     */
    public function match(string $table): bool
    {
        $patterns = $this->get('patterns', []);
        foreach ($patterns as $pattern) {
            if (intval(preg_match('/' . $pattern . '/', $table)) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * GEt relative path.
     *
     * @return string
     * @throws ConfigException
     */
    private function getRelativePath(): string
    {
        // Validate if relative path is specified.
        $pathRelative = $this->get('relative');
        if ($pathRelative === null) {
            throw new ConfigException('Package relative path {relative} is not specified.');
        }

        return $pathRelative;
    }

    /**
     * Get absolute path.
     *
     * @return string
     * @throws ConfigException
     */
    private function getAbsolutePath(): string
    {
        // Validate if absolute path is specified.
        $pathAbsolute = $this->get('package');
        if ($pathAbsolute === null) {
            throw new ConfigException('Package absolute path {package} is not specified.');
        }

        // Validate path existence.
        $pathAbsolute = rtrim($pathAbsolute, '/');
        if (!is_dir($pathAbsolute)) {
            throw new ConfigException('Path "' . $pathAbsolute . '" does not exist.');
        }

        // Validate "composer.json" existence.
        if (!file_exists($pathAbsolute . '/composer.json')) {
            throw new ConfigException('"composer.json" not found in "' . $pathAbsolute . '".');
        }

        return $pathAbsolute;
    }

    /**
     * Get.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed|null
     */
    private function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $default;
    }
}
