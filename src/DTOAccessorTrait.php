<?php

namespace CodinPro\DataTransferObject;

use InvalidArgumentException;

/**
 * @property mixed $innerDTOData DTO data
 * @property array $innerDTODefault DTO keys and default values
 */
trait DTOAccessorTrait
{
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Get data at offset or default value instead
     * @param  string  $offset
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Get value by offset or nested structure
     * @param  string  $offset
     * @return mixed
     */
    abstract public function get(string $offset): mixed;

    /**
     * Set data at offset
     * @param  string  $offset
     * @param  mixed  $value
     */
    public function offsetSet($offset, mixed $value): void
    {
        $this->innerDTOData[$offset] = $value;
    }

    public function __isset($key)
    {
        return isset($this->innerDTOData[$key]);
    }

    /**
     * Count data elements
     * @return int
     */
    public function count(): int
    {
        return count($this->innerDTOData);
    }

    /**
     * Check if offset exists
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->innerDTOData[$offset]);
    }

    /**
     * Remove data at offset
     * @param  string  $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->innerDTOData[$offset]);
    }

    /**
     * Get data at scalar offset or default value instead
     * @param  string  $offset
     * @param  bool  $strict
     * @return mixed
     */
    private function offsetGetScalar(string $offset, bool $strict = false): mixed
    {
        if (array_key_exists($offset, $this->innerDTOData)) {
            return $this->innerDTOData[$offset];
        }

        return $this->getDefaultValue($offset, $strict);
    }

    /**
     * Get default value at offset if set
     * @param  string  $offset
     * @param  bool  $strict
     * @return mixed
     */
    private function getDefaultValue(string $offset, bool $strict = false): mixed
    {
        if (array_key_exists($offset, $this->innerDTODefault)) {
            return $this->innerDTODefault[$offset];
        }

        if ($strict) {
            throw new InvalidArgumentException('Offset ' . $offset . ' does not exist.');
        }

        return null;
    }
}
