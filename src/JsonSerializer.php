<?php

namespace CodinPro\DataTransferObject;

class JsonSerializer implements DTOSerializerInterface
{
    private $options;
    private $depth;

    /**
     * DTOSerializer constructor.
     * @param int $options json_encode options (default: 0)
     * @param int $depth json_encode depth (default: 512)
     */
    public function __construct($options = 0, $depth = 512)
    {
        $this->options = $options;
        $this->depth = $depth;
    }

    /**
     * Serialize given data to string
     * @param $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($data, $this->options, $this->depth);
    }

    /**
     * @return int
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $options
     * @return JsonSerializer
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param int $depth
     * @return JsonSerializer
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }
}