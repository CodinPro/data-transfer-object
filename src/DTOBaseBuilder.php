<?php

namespace CodinPro\DataTransferObject;

use InvalidArgumentException;
use JsonException;

class DTOBaseBuilder
{
    private DTOBase $dto;

    public function __construct(DTOBase $dtoBase)
    {
        $this->dto = $dtoBase;
    }

    /**
     * Build DTO from given type of data
     * @param $data
     * @throws InvalidArgumentException
     */
    public function build($data): void
    {
        switch (gettype($data)) {
            case 'array':
                $this->buildFromArray($data);
                break;
            case 'object':
                $this->buildFromObject($data);
                break;
            case 'string':
                $this->buildFromJson($data);
                break;
            default:
                throw new InvalidArgumentException('DTO can be built from array|object|json, "' . gettype($data) . '" given.');
        }
    }

    /**
     * Build DTO from provided data
     * @param $array
     */
    private function buildFromArray($array): void
    {
        $this->validateFieldNames($array);

        foreach ($this->dto->getDefault() as $key => $value) {
            if (isset($array[$key])) {
                $this->dto[$key] = $array[$key];
            } else {
                $this->dto[$key] = $value;
            }
        }
    }

    /**
     * Restrict internalDTO* fields in data
     * @param $data
     */
    private function validateFieldNames($data): void
    {
        $restrictedFields = ['internalDTOData', 'internalDTODefault'];
        foreach ($restrictedFields as $field) {
            if ($this->arrayHasField($data, $field) || $this->objectHasField($data, $field)) {
                throw new InvalidArgumentException('internalDTO* fields are restricted');
            }
        }
    }

    /**
     * Check if given array has given key
     * @param $array
     * @param $field
     * @return bool
     */
    private function arrayHasField($array, $field): bool
    {
        return is_array($array) && isset($array[$field]);
    }

    /**
     * Check if given object has given property
     * @param $object
     * @param $field
     * @return bool
     */
    private function objectHasField($object, $field): bool
    {
        return is_object($object) && isset($object->{$field});
    }

    /**
     * Build DTO from provided data
     * @param $object
     */
    private function buildFromObject($object): void
    {
        $this->validateFieldNames($object);

        foreach ($this->dto->getDefault() as $key => $value) {
            if (isset($object->{$key})) {
                $this->dto[$key] = $object->{$key};
            } else {
                $this->dto[$key] = $value;
            }
        }
    }

    /**
     * Try to build from provided string as JSON
     * @param  string  $data
     * @throws JsonException
     */
    private function buildFromJson(string $data): void
    {
        $triedToDecodeData = json_decode($data, false, 512, JSON_THROW_ON_ERROR);

        if (is_object($triedToDecodeData)) {
            $this->buildFromObject($triedToDecodeData);
        }
    }
}
