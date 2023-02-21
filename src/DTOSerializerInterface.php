<?php

namespace CodinPro\DataTransferObject;

interface DTOSerializerInterface
{
    /**
     * Serialize given data to string
     * @param $data
     * @return string
     */
    public function serialize($data): string;
}
