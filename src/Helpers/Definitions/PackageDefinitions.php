<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers\Definitions;

class PackageDefinitions
{
    /** @var PackageDefinition[] */
    private $packages;

    /**
     * ConstantDefinition constructor.
     *
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->packages = [];
        foreach ($data as $package => $packageData) {
            $this->packages[$package] = new PackageDefinition($packageData);
        }
    }

    /**
     * Get package match.
     *
     * @param string $table
     * @return PackageDefinition|null
     */
    public function getPackageMatch(string $table): ?PackageDefinition
    {
        foreach ($this->packages as $packageDefinition) {
            if ($packageDefinition->match($table)) {
                return $packageDefinition;
            }
        }

        return null;
    }
}
