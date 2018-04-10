<?php

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
