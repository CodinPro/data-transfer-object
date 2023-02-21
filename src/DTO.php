<?php

namespace CodinPro\DataTransferObject;

use JsonException;

class DTO extends DTOBase
{
    /**
     * DTO constructor.
     * @param  array|object|string  $data
     * @param  DTOSerializerInterface|null  $serializer
     * @throws JsonException
     */
    public function __construct($data = [], $serializer = null)
    {
        parent::__construct($this->collectVariables(), $data, $serializer);
    }

    /**
     * Get object variables defined in DTO
     * @return array
     */
    private function collectVariables(): array
    {
        $currentVariables = get_object_vars($this);
        $parentVariables = get_class_vars(parent::class);

        $currentDTOKeys = array_diff(array_keys($currentVariables), array_keys($parentVariables));

        $currentDTOValues = [];
        foreach ($currentDTOKeys as $key) {
            $currentDTOValues[$key] = $currentVariables[$key];
        }

        return $currentDTOValues;
    }
}