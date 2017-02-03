<?php
/**
 * Created by PhpStorm.
 * User: vlitvinovs
 * Date: 2/2/17
 * Time: 5:14 PM
 */

namespace CodinPro\DataTransferObject;

interface DTOSerializerInterface
{
    /**
     * Serialize given data to string
     * @param $data
     * @return string
     */
    public function serialize($data);
}