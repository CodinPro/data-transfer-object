<?php

namespace CodinPro\DataTransferObject;

use JsonException;

class JsonSerializer implements DTOSerializerInterface
{
    private int $options;
    private int $depth;

    /**
     * DTOSerializer constructor.
     * @param  int  $options  json_encode options (default: 0)
     * @param  int  $depth  json_encode depth (default: 512)
     */
    public function __construct(int $options = 0, int $depth = 512)
    {
        $this->options = $options;
        $this->depth = $depth;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @param  int  $depth
     * @return JsonSerializer
     */
    public function setDepth(int $depth): static
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }

    /**
     * @param  int  $options
     * @return JsonSerializer
     */
    public function setOptions(int $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Serialize given data to string
     * @param $data
     * @return string
     * @throws JsonException
     */
    public function serialize($data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR | $this->options, $this->depth);
    }
}