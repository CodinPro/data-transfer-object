<?php

namespace CodinPro\DataTransferObject;

use ArrayAccess;
use Countable;
use IteratorAggregate;

class DTOBase implements ArrayAccess, IteratorAggregate, Countable
{
    protected $data;
    protected $default = [];
    private $serializer = null;

    /**
     * DTO constructor.
     * @param array $default
     * @param array|object|string $data
     * @param DTOSerializerInterface $serializer
     * @throws \InvalidArgumentException
     */
    public function __construct($default = [], $data = [], DTOSerializerInterface $serializer = null)
    {
        if (count($default) > 0) {
            $this->default = $default;
        }

        $this->serializer = $serializer === null ? new JsonSerializer() : $serializer;

        $this->build($data);
    }

    /**
     * Build DTO from given type of data
     * @param $data
     * @throws \InvalidArgumentException
     */
    private function build($data)
    {
        switch (gettype($data)) {
            case 'array':
                $this->buildFromData($data);
                break;
            case 'object':
                $this->buildFromData($data);
                break;
            case 'string':
                $triedToDecodeData = json_decode($data);

                if ($triedToDecodeData !== null) {
                    $this->buildFromData($triedToDecodeData);
                } else {
                    throw new \InvalidArgumentException(
                        'DTO can be built from array|object|json, "'.gettype($data).'" given. Probably tried to pass invalid JSON.'
                    );
                }
                break;
            default:
                throw new \InvalidArgumentException('DTO can be built from array|object|json, "'.gettype($data).'" given.');
        }
    }

    /**
     * Build DTO from provided data
     * @param object|array $data
     */
    private function buildFromData($data)
    {
        foreach ($this->default as $key => $value) {
            if (is_object($data) && isset($data->{$key})) {
                $this->data[$key] = $data->{$key};
            } else if (is_array($data) && isset($data[$key])) {
                $this->data[$key] = $data[$key];
            } else {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Get custom iterator
     * @return DTOIterator
     */
    public function getIterator()
    {
        return new DTOIterator($this->data);
    }

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

        return $this->getDefault($offset);
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

    /**
     * Get default value at offset if set
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function getDefault($offset)
    {
        if (isset($this->default[$offset])) {
            return $this->default[$offset];
        }

        throw new \InvalidArgumentException('Offset '.$offset.' does not exist.');
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

    /**
     * Get nested values using "dot" notation
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($offset)
    {
        if (strpos($offset, '.') === false) {
            return $this->offsetGetScalar($offset);
        } else {
            $keys = explode('.', $offset);
            $scope = $this->data;
            foreach ($keys as $key) {
                $isAccessibleArray = (is_array($scope) || $scope instanceof ArrayAccess) && isset($scope[$key]);
                $isAccessibleObject = is_object($scope) && isset($scope->{$key});

                if ($isAccessibleArray) {
                    $scope = $scope[$key];
                } elseif ($isAccessibleObject) {
                    $scope = $scope->{$key};
                } else {
                    throw new \InvalidArgumentException('Non existent offset given in offset chain: '.$key);
                }
            }

            return $scope;
        }
    }

    /**
     * Converts data to string
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * Serializes the data using serializer
     * @return string
     */
    private function serialize()
    {
        if ($this->serializer === null) {
            return 'Serializer not set';
        }

        return $this->serializer->serialize($this->data);
    }

    /**
     * @return DTOSerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param DTOSerializerInterface $serializer
     * @return DTOBase
     */
    public function setSerializer(DTOSerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }
}