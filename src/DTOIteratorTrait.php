<?php

namespace CodinPro\DataTransferObject;

/**
 * @property mixed $innerDTOData DTO data
 * @property array $innerDTODefault DTO keys and default values
 */
trait DTOIteratorTrait
{
    /**
     * Get custom iterator
     * @return DTOIterator
     */
    public function getIterator(): DTOIterator
    {
        return new DTOIterator($this->innerDTOData);
    }
}
