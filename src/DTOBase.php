<?php

namespace CodinPro\DataTransferObject;

use ArrayAccess;
use Countable;
use IteratorAggregate;

class DTOBase implements ArrayAccess, IteratorAggregate, Countable
{
    protected $innerDTOData;
    protected $default = [];
    private $serializer = null;

    use DTOAccessorTrait;
    use DTOIteratorTrait;

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

        (new DTOBaseBuilder($this))->build($data);
    }

    /**
     * Get value by key or nested structure
     * @param string $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function get($offset)
    {
        if (strpos($offset, '.') === false) {
            return $this->offsetGetScalar($offset);
        } else {
            return $this->processChain($offset);
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
     * Converts data to string
     * @return string
     */
    public function toArray()
    {
        if ($this->serializer instanceof JsonSerializer) {
            return json_decode($this->serialize(), true);
        } else {
            return json_decode((new JsonSerializer())->serialize($this->innerDTOData), true);
        }
    }

    /**
     * Serializes the data using serializer
     * @return string
     */
    private function serialize()
    {
        return $this->serializer->serialize($this->innerDTOData);
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

    /**
     * @return array
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get nested values using "dot" notation
     * @param $offset
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function processChain($offset)
    {
        $keys = explode('.', $offset);
        $scope = $this->innerDTOData;
        foreach ($keys as $key) {
            $scope = $this->pickValue($scope, $key);
        }

        return $scope;
    }

    /**
     * @param $scope
     * @param $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    private function pickValue($scope, $key)
    {
        if (isset($scope->{$key})) {
            return $scope->{$key};
        } elseif (isset($scope[$key])) {
            return $scope[$key];
        }

        throw new \InvalidArgumentException('Non existent offset given in offset chain: '.$key);
    }
}