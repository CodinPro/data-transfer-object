<?php
/**
 * Created by PhpStorm.
 * User: vlitvinovs
 * Date: 2/8/17
 * Time: 4:35 PM
 */

namespace CodinPro\DataTransferObject;

trait DTOIteratorTrait
{
    /**
     * @property $data
     */

    /**
     * Get custom iterator
     * @return DTOIterator
     */
    public function getIterator()
    {
        return new DTOIterator($this->data);
    }
}