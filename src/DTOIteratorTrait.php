<?php
/**
 * Created by PhpStorm.
 * User: vlitvinovs
 * Date: 2/8/17
 * Time: 4:35 PM
 */

namespace CodinPro\DataTransferObject;

/**
 * @property mixed $data DTO data
 * @property array $default DTO keys and default values
 */
trait DTOIteratorTrait
{
    /**
     * Get custom iterator
     * @return DTOIterator
     */
    public function getIterator()
    {
        return new DTOIterator($this->data);
    }
}