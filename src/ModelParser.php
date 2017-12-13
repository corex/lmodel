<?php

namespace CoRex\Laravel\Model;

use Illuminate\Support\Str;

class ModelParser
{
    private $lines;

    /**
     * ModelParser constructor.
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->lines = [];
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            if ($content !== null && $content != '') {
                $content = str_replace("\r", '', $content);
                $this->lines = explode("\n", $content);
            }
        }
    }

    /**
     * Get uses.
     *
     * @return array
     */
    public function getUses()
    {
        // Get existing uses.
        $uses = Arr::lineMatch($this->lines, 'use ', ';', true, true);

        // Make sure if extends is specified, it is added.
        $extends = Config::value('extends', Constants::DEFAULT_MODEL_CLASS);
        if (!in_array($extends, $uses)) {
            $uses[] = $extends;
        }
        $uniqueUses = array_unique($uses);
        sort($uniqueUses);
        return $uniqueUses;
    }

    /**
     * Get preserved lines.
     *
     * @return array
     */
    public function getPreservedLines()
    {
        if ($this->lines === null || count($this->lines) == 0) {
            return [];
        }
        $lines = [];
        $add = false;
        foreach ($this->lines as $line) {
            if (Str::startsWith($line, '}')) {
                $add = false;
            }
            if ($add) {
                if (substr($line, 0, strlen(Constants::DEFAULT_INDENT)) == Constants::DEFAULT_INDENT) {
                    $line = substr($line, strlen(Constants::DEFAULT_INDENT));
                }
                if (substr($line, 0, 1) == "\t") {
                    $line = substr($line, 1);
                }
                $lines[] = $line;
            }
            if (!$add && Str::contains($line, Constants::PRESERVED_IDENTIFIER)) {
                $add = true;
            }
        }
        return $lines;
    }

    /**
     * Get timestamps.
     *
     * @return boolean
     */
    public function getTimestamps()
    {
        $timestamps = 'false';
        foreach ($this->lines as $line) {
            if (Str::contains($line, 'public $timestamps')) {
                $line = explode(' ', $line);
                $timestamps = trim(last($line), ';');
            }
        }
        return strtolower($timestamps) == 'true';
    }
}