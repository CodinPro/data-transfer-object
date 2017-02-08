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
    /** @var DTOBase $dto */
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
            case 'object':
                $this->buildFromData($data);
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
     * @param object|array $data
     */
    private function buildFromData($data)
    {
        foreach ($this->dto->getDefault() as $key => $value) {
            if (is_object($data) && isset($data->{$key})) {
                $this->dto[$key] = $data->{$key};
            } else if (is_array($data) && isset($data[$key])) {
                $this->dto[$key] = $data[$key];
            } else {
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
            $this->buildFromData($triedToDecodeData);
        } else {
            throw new \InvalidArgumentException(
                'DTO can be built from array|object|json, "'.gettype($data).'" given. Probably tried to pass invalid JSON.'
            );
        }
    }
}