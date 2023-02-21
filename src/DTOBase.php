<?php

namespace CodinPro\DataTransferObject;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonException;

class DTOBase implements ArrayAccess, IteratorAggregate, Countable
{
    protected $innerDTOData;
    protected $innerDTODefault = [];
    private ?DTOSerializerInterface $serializer;
    use DTOAccessorTrait;
    use DTOIteratorTrait;

    /**
     * DTO constructor.
     * @param  array  $default
     * @param  array  $data
     * @param  DTOSerializerInterface|null  $serializer
     * @throws JsonException
     */
    public function __construct(array $default = [], mixed $data = [], DTOSerializerInterface $serializer = null)
    {
        if (count($default) > 0) {
            $this->innerDTODefault = $default;
        }

        $this->serializer = $serializer ?? new JsonSerializer();

        (new DTOBaseBuilder($this))->build($data);
    }

    /**
     * Converts data to string
     * @return string
     * @throws JsonException
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * Serializes the data using serializer
     * @return string
     * @throws JsonException
     */
    private function serialize(): string
    {
        return $this->serializer->serialize($this->innerDTOData);
    }

    /**
     * Get value by key or nested structure
     * @param  string  $offset
     * @param  bool  $strict
     * @return mixed
     */
    public function get(string $offset, bool $strict = false): mixed
    {
        if (!str_contains($offset, '.')) {
            return $this->offsetGetScalar($offset, $strict);
        }

        return $this->processChain($offset);
    }

    /**
     * Get nested values using "dot" notation
     * @param $offset
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function processChain($offset): mixed
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
     * @throws InvalidArgumentException
     */
    private function pickValue($scope, $key): mixed
    {
        if (isset($scope->{$key})) {
            return $scope->{$key};
        }

        if (isset($scope[$key])) {
            return $scope[$key];
        }

        throw new InvalidArgumentException('Non existent offset given in offset chain: ' . $key);
    }

    /**
     * @return array
     */
    public function getDefault(): array
    {
        return $this->innerDTODefault;
    }

    /**
     * @return DTOSerializerInterface|null
     */
    public function getSerializer(): ?DTOSerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param  DTOSerializerInterface  $serializer
     * @return DTOBase
     */
    public function setSerializer(DTOSerializerInterface $serializer): static
    {
        $this->serializer = $serializer;

        return $this;
    }

    /**
     * Converts data to array
     * @return array
     * @throws JsonException
     */
    public function toArray(): array
    {
        if ($this->serializer instanceof JsonSerializer) {
            return json_decode($this->serialize(), true, 512, JSON_THROW_ON_ERROR);
        }

        return json_decode((new JsonSerializer())->serialize($this->innerDTOData), true, 512, JSON_THROW_ON_ERROR);
    }
}
