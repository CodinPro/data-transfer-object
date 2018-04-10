<?php

namespace CodinPro\DataTransferObject;

/**
 * @property mixed $innerDTOData DTO data
 * @property array $innerDTODefault DTO keys and default values
 */
trait DTOAccessorTrait
{
    /**
     * Get value by offset or nested structure
     * @param string $offset
     * @return mixed
     */
    public abstract function get($offset);

    /**
     * Check if offset exists
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->innerDTOData[$offset]);
    }

    /**
     * Get data at scalar offset or default value instead
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function offsetGetScalar($offset)
    {
        if (array_key_exists($offset, $this->innerDTOData)) {
            return $this->innerDTOData[$offset];
        }

        return $this->getDefaultValue($offset);
    }

    /**
     * Get default value at offset if set
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getDefaultValue($offset)
    {
        if (array_key_exists($offset, $this->innerDTODefault)) {
            return $this->innerDTODefault[$offset];
        }

        throw new \InvalidArgumentException('Offset '.$offset.' does not exist.');
    }

    /**
     * Get data at offset or default value instead
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set data at offset
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->innerDTOData[$offset] = $value;
    }

    /**
     * Remove data at offset
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->innerDTOData[$offset]);
    }

    /**
     * Count data elements
     * @return int
     */
    public function count()
    {
        return count($this->innerDTOData);
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function __isset($key)
    {
        return isset($this->innerDTOData[$key]);
    }
}
