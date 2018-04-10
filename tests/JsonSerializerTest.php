<?php

namespace CodinPro\DataTransferObject;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializer()
    {
        $serializer = new JsonSerializer();
        $data = ['foo' => 'bar'];
        $this->assertEquals(json_encode($data), $serializer->serialize($data));
    }

    public function testDepthAndOptions()
    {
        $serializer = new JsonSerializer();
        $data = ['foo' => ['bar' => 'baz']];

        $this->assertEquals(json_encode($data), $serializer->serialize($data));

        $this->assertEquals(0, $serializer->getOptions());
        $serializer->setOptions(JSON_PRETTY_PRINT);
        $this->assertEquals(JSON_PRETTY_PRINT, $serializer->getOptions());

        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $serializer->serialize($data));

        $this->assertEquals(512, $serializer->getDepth());
        $serializer->setDepth(1);
        $this->assertEquals(1, $serializer->getDepth());

        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT, 1), $serializer->serialize($data));
    }
}
