<?php
/**
 * Created by PhpStorm.
 * User: vlitvinovs
 * Date: 2/6/17
 * Time: 9:51 AM
 */

namespace CodinPro\DataTransferObject;

class CustomSerializer implements DTOSerializerInterface
{

    /**
     * Serialize given data to string
     * @param $data
     * @return string
     */
    public function serialize($data)
    {
        return serialize($data);
    }
}