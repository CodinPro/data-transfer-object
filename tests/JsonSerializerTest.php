<?php

namespace CodinPro\Tests\DataTransferObject;

use CodinPro\DataTransferObject\JsonSerializer;
use JsonException;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testDepthAndOptions(): void
    {
        $serializer = new JsonSerializer();
        $data = ['foo' => ['bar' => 'baz']];

        $this->assertEquals(json_encode($data, JSON_THROW_ON_ERROR), $serializer->serialize($data));

        $this->assertEquals(0, $serializer->getOptions());
        $serializer->setOptions(JSON_PRETTY_PRINT);
        $this->assertEquals(JSON_PRETTY_PRINT, $serializer->getOptions());

        $this->assertEquals(json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), $serializer->serialize($data));

        $this->assertEquals(512, $serializer->getDepth());
        $serializer->setDepth(10);
        $this->assertEquals(10, $serializer->getDepth());

        $this->assertEquals(json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT, 10), $serializer->serialize($data));
    }

    /**
     * @throws JsonException
     */
    public function testSerializer(): void
    {
        $serializer = new JsonSerializer();
        $data = ['foo' => 'bar'];
        $this->assertEquals(json_encode($data, JSON_THROW_ON_ERROR), $serializer->serialize($data));
    }
}
