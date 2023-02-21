<?php

namespace CodinPro\DataTransferObject;

use Iterator;
use ReturnTypeWillChange;

class DTOIterator implements Iterator
{
    private array $data;
    private ?array $keys = null;
    protected int $position;

    /**
     * DTOIterator constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get current element
     * @return mixed
     */
    public function current(): mixed
    {
        return $this->data[$this->key()];
    }

    /**
     * Next iteration
     */
    #[ReturnTypeWillChange] public function next(): void
    {
        $this->position++;
    }

    /**
     * Get current key
     * @return mixed
     */
    public function key(): mixed
    {
        return $this->getKeys()[$this->position];
    }

    /**
     * Check if current position key exists
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->getKeys()[$this->position]);
    }

    /**
     * Restart iterator
     */
    #[ReturnTypeWillChange] public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Get array of keys and store them
     * @return array|null
     */
    public function getKeys(): ?array
    {
        if ($this->keys === null) {
            $this->keys = array_keys($this->data);
        }

        return $this->keys;
    }

}