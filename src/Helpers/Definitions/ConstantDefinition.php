<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers\Definitions;

use CoRex\Laravel\Model\Constants;

class ConstantDefinition
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
     * Get title.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->get('title');
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getNameColumn(): ?string
    {
        return $this->get('name');
    }

    /**
     * Get value.
     *
     * @return string|null
     */
    public function getValueColumn(): ?string
    {
        return $this->get('value');
    }

    /**
     * Get name prefix.
     *
     * @return string|null
     */
    public function getNamePrefix(): ?string
    {
        return $this->get('prefix');
    }

    /**
     * Get name suffix.
     *
     * @return string|null
     */
    public function getNameSuffix(): ?string
    {
        return $this->get('suffix');
    }

    /**
     * Get name replace(s).
     *
     * @return mixed[]
     */
    public function getNameReplace(): array
    {
        return array_merge(
            Constants::STANDARD_REPLACES,
            $this->get('replace', [])
        );
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
