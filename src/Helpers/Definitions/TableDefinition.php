<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Laravel\Model\Constants;

class TableDefinition
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
     * Get output name.
     *
     * @return string|null
     */
    public function getOutputName(): ?string
    {
        return $this->data['output_name'] ?? null;
    }

    /**
     * Get timestamps "created_at".
     *
     * @return string|null
     */
    public function getTimestampsCreatedAt(): ?string
    {
        return $this->get(Constants::ELOQUENT_CREATED_AT, Constants::ELOQUENT_CREATED_AT);
    }

    /**
     * Get timestamps "updated_at".
     *
     * @return string|null
     */
    public function getTimestampsUpdatedAt(): ?string
    {
        return $this->get(Constants::ELOQUENT_UPDATED_AT, Constants::ELOQUENT_UPDATED_AT);
    }

    /**
     * Get timestamps date format.
     *
     * @return string|null
     */
    public function getTimestampsDateFormat(): ?string
    {
        return $this->get('date_format');
    }

    /**
     * Get fillable columns.
     *
     * @return string[]
     */
    public function getFillableColumns(): array
    {
        return $this->get('fillable', []);
    }

    /**
     * Get guarded columns.
     *
     * @return string[]
     */
    public function getGuardedColumns(): array
    {
        return $this->get('guarded', []);
    }

    /**
     * Get readonly columns.
     *
     * @return string[]
     */
    public function getReadonlyColumns(): array
    {
        return $this->get('readonly', []);
    }

    /**
     * Get hidden attributes.
     *
     * @return array|null
     */
    public function getHiddenAttributes(): array
    {
        return $this->get('hidden', []);
    }

    /**
     * Get cast attributes.
     *
     * @return array|null
     */
    public function getCastAttributes(): array
    {
        return $this->get('casts', []);
    }

    /**
     * Get accessors.
     *
     * @return array|null
     */
    public function getAccessors(): array
    {
        return $this->get('appends', []);
    }

    /**
     * Get constant definitions.
     *
     * @return ConstantDefinition[]
     */
    public function getConstantDefinitions(): array
    {
        $definitions = $this->get('constants', []);

        // Convert list to list of objects.
        if (count($definitions) > 0) {
            foreach ($definitions as $index => $definition) {
                $definitions[$index] = new ConstantDefinition($definition);
            }
        }

        return $definitions;
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
