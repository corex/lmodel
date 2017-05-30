<?php

namespace CoRex\Laravel\Model;

use Illuminate\Support\Str;

class Arr
{
    /**
     * Get line match.
     *
     * @param array $lines
     * @param string $prefix
     * @param string $suffix
     * @param boolean $doTrim
     * @param boolean $removePrefixSuffix Default false.
     * @return array
     */
    public static function lineMatch(array $lines, $prefix, $suffix, $doTrim, $removePrefixSuffix = false)
    {
        $result = [];
        foreach ($lines as $line) {
            $isHit = true;
            if ($prefix != '' && $prefix !== null && Str::startsWith(trim($line), $prefix)) {
                if ($removePrefixSuffix) {
                    $line = substr(trim($line), strlen($prefix));
                }
            } else {
                $isHit = false;
            }
            if ($suffix != '' && $suffix !== null && Str::endsWith(trim($line), $suffix)) {
                if ($removePrefixSuffix) {
                    $line = substr(trim($line), 0, -strlen($suffix));
                }
            } else {
                $isHit = false;
            }
            if ($isHit) {
                if ($doTrim) {
                    $line = trim($line);
                }
                $result[] = $line;
            }
        }
        return $result;
    }
}