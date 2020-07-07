<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers;

use CoRex\Laravel\Model\Constants;
use CoRex\Laravel\Model\Exceptions\ModelException;
use CoRex\Laravel\Model\Interfaces\ParserInterface;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionException;

class Parser implements ParserInterface
{
    /** @var string */
    private $filename;

    /** @var string[] */
    private $lines;

    /** @var string */
    private $class;

    /** @var string[] */
    private $uses = [];

    /** @var ReflectionClass */
    private $reflectionClass;

    /**
     * Set filename.
     *
     * @param string $filename
     * @throws ModelException
     * @throws ReflectionException
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;

        // Get lines from file.
        $lines = [];
        if (File::exists($this->filename)) {
            $content = File::get($this->filename);
            $content = str_replace("\r", '', $content);
            $lines = explode("\n", $content);
        }

        $this->lines = $lines;

        if (count($lines) === 0) {
            return;
        }

        // Scan for class.
        $namespace = null;
        $class = null;
        $this->uses = [];
        foreach ($this->lines as $line) {
            // Extract namespace.
            if (substr($line, 0, 10) === 'namespace ') {
                $line = trim(substr($line, 10));
                $namespace = substr($line, 0, strpos($line, ';'));
            }

            // Extract class.
            if (substr($line, 0, 6) === 'class ') {
                $line = trim(substr($line, 6));
                $class = substr($line, 0, strpos($line, ' '));
            }

            // Extract uses.
            if (substr($line, 0, 4) === 'use ') {
                $this->uses[] = trim(substr($line, 4, -1));
            }
        }

        // Validate extraction of namespace.
        if ($namespace === null) {
            throw new ModelException('Not possible to extract namespace from "' . $this->filename . '".');
        }

        // Validate extraction of class.
        if ($class === null) {
            throw new ModelException('Not possible to extract class from "' . $this->filename . '".');
        }

        $this->class = $namespace . '\\' . $class;

        // Validate class existence.
        if (!class_exists($this->class)) {
            throw new ModelException('Class "' . $this->class . '" does not exist.');
        }

        $this->reflectionClass = new ReflectionClass($this->class);
    }

    /**
     * Get uses.
     *
     * @return string[]
     */
    public function getUses(): array
    {
        return $this->exists() ? $this->uses : [];
    }

    /**
     * Get traits.
     *
     * @return string[]
     */
    public function getTraits(): array
    {
        return $this->exists() ? $this->reflectionClass->getTraitNames() : [];
    }

    /**
     * Get preserved lines.
     *
     * @return string[]
     */
    public function getPreservedLines(): array
    {
        $preservedLines = [];

        if (!$this->exists()) {
            return $preservedLines;
        }

        $preservedIdentifierFound = false;
        foreach ($this->lines as $line) {
            if ($line === '}') {
                $preservedIdentifierFound = false;
            }

            if ($preservedIdentifierFound) {
                $preservedLines[] = $line;
            }

            if (strpos($line, Constants::PRESERVED_IDENTIFIER) !== false) {
                $preservedIdentifierFound = true;
            }
        }

        return $preservedLines;
    }

    /**
     * Exists.
     *
     * @return bool
     */
    private function exists(): bool
    {
        return count($this->lines) > 0;
    }
}
