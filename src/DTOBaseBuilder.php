<?php
/**
 * Created by PhpStorm.
 * User: vlitvinovs
 * Date: 2/8/17
 * Time: 4:28 PM
 */

namespace CodinPro\DataTransferObject;

class DTOBaseBuilder
{
    /** @property DTOBase $dto */
    private $dto;

    public function __construct(DTOBase $dtoBase)
    {
        $this->dto = $dtoBase;
    }

    /**
     * Build DTO from given type of data
     * @param $data
     * @throws \InvalidArgumentException
     */
    public function build($data)
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
                throw new \InvalidArgumentException('DTO can be built from array|object|json, "'.gettype($data).'" given.');
        }
    }

    /**
     * Build DTO from provided data
     * @param $array
     */
    private function buildFromArray($array)
    {
        foreach ($this->dto->getDefault() as $key => $value) {
            if (isset($array[$key])) {
                $this->dto[$key] = $array[$key];
            } else {
                $this->dto[$key] = $value;
            }
        }
    }

    /**
     * Build DTO from provided data
     * @param $object
     */
    private function buildFromObject($object)
    {
        foreach ($this->dto->getDefault() as $key => $value) {
            if (isset($object->{$key})) {
                $this->dto[$key] = $object->{$key};
            }else {
                $this->dto[$key] = $value;
            }
        }
    }

    /**
     * Try to build from provided string as JSON
     * @param string $data
     * @throws \InvalidArgumentException
     */
    private function buildFromJson($data)
    {
        $triedToDecodeData = json_decode($data);

        if ($triedToDecodeData !== null) {
            $this->buildFromObject($triedToDecodeData);
        } else {
            throw new \InvalidArgumentException(
                'DTO can be built from array|object|json, "'.gettype($data).'" given. Probably tried to pass invalid JSON.'
            );
        }
    }
}