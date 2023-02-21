<?php

namespace CodinPro\DataTransferObject;

class CustomSerializer implements DTOSerializerInterface
{
    /**
     * Serialize given data to string
     * @param $data
     * @return string
     */
    public function serialize($data): string
    {
        return serialize($data);
    }
}
