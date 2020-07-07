<?php

declare(strict_types=1);

namespace CoRex\Laravel\Model\Helpers\Database;

use CoRex\Laravel\Model\Interfaces\ColumnInterface;

class Column implements ColumnInterface
{
    /** @var mixed[] */
    private $data;

    /**
     * Column constructor.
     *
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->get('name');
    }

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->get('type');
    }

    /**
     * Get comment.
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->get('comment');
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
