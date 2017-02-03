<?php

namespace CodinPro\DataTransferObject;

use Iterator;

class DTOIterator implements Iterator
{
    /** @var array $data */
    private $data;
    /** @var array $keys */
    private $keys = null;
    /** @var int $position Current iterator position */
    protected $position;

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
    public function current()
    {
        return $this->data[$this->key()];
    }

    /**
     * Next iteration
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Get current key
     * @return mixed
     */
    public function key()
    {
        return $this->getKeys()[$this->position];
    }

    /**
     * Check if current position key exists
     * @return bool
     */
    public function valid()
    {
        return isset($this->getKeys()[$this->position]);
    }

    /**
     * Restart iterator
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Get array of keys and store them
     * @return array
     */
    public function getKeys()
    {
        if ($this->keys === null) {
            $this->keys = array_keys($this->data);
        }

        return $this->keys;
    }

}