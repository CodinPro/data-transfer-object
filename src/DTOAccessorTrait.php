<?php
/**
 * Created by PhpStorm.
 * User: vlitvinovs
 * Date: 2/8/17
 * Time: 4:34 PM
 */

namespace CodinPro\DataTransferObject;

trait DTOAccessorTrait
{
    /**
     * Check if offset exists
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Get data at scalar offset or default value instead
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function offsetGetScalar($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
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
        if (isset($this->default[$offset])) {
            return $this->default[$offset];
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
        $this->data[$offset] = $value;
    }

    /**
     * Remove data at offset
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Count data elements
     * @return int
     */
    public function count()
    {
        return count($this->data);
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
        return isset($this->data[$key]);
    }
}