<?php

namespace CodinPro\DataTransferObject;

class ExampleDTOTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildFromArray()
    {
        $dto = new ExampleDTO();

        $this->assertEquals(true, $dto->get('foo'));
        $this->assertEquals('string', $dto->get('bar'));
        $this->assertEquals(['a' => 'b'], $dto->get('extra'));
    }

    public function testBuildFromJson()
    {
        $dto = new ExampleDTO('{"foo":false,"extra":"some extra value"}');

        $this->assertEquals(false, $dto->get('foo'));
        $this->assertEquals('string', $dto->get('bar'));
        $this->assertEquals('some extra value', $dto->get('extra'));
    }

    public function testBuildFromObject()
    {
        $object = json_decode('{"foo":false,"extra":"some extra value"}');

        $this->assertEquals('object', gettype($object));

        $dto = new ExampleDTO($object);

        $this->assertEquals(false, $dto->get('foo'));
        $this->assertEquals('string', $dto->get('bar'));
        $this->assertEquals('some extra value', $dto->get('extra'));
    }

    public function testBuildFromInvalidJson()
    {
        try {
            $dto = new ExampleDTO('"test1":"one","test3":"three"');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals(
                'DTO can be built from array|object|json, "string" given. Probably tried to pass invalid JSON.',
                $e->getMessage()
            );
        }

        $this->assertFalse(isset($dto));
    }

    public function testBuildFromInvalidDataType()
    {
        try {
            $dto = new ExampleDTO(123);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals(
                'DTO can be built from array|object|json, "integer" given.',
                $e->getMessage()
            );
        }

        $this->assertFalse(isset($dto));
    }

    public function testSetValue()
    {
        $dto = new ExampleDTO();
        $dto['test4'] = 'four';

        $this->assertEquals('four', $dto->get('test4'));
    }

    public function testCount()
    {
        $dto = new ExampleDTO();

        $this->assertCount(3, $dto);
    }

    public function testOffsetExists()
    {
        $dto = new ExampleDTO();

        $this->assertNotEmpty($dto['foo']);
        $this->assertTrue(isset($dto['foo']));
    }

    public function testUnset()
    {
        $dto = new ExampleDTO(['foo' => false]);

        $this->assertEquals(false, $dto->get('foo'));

        unset($dto['foo']);

        $this->assertEquals(true, $dto->get('foo'));
    }

    public function testGetNonExistantKey()
    {
        $dto = new ExampleDTO();

        $key = 'nonExistantKey';

        try {
            $dto->get($key);
        } catch (\Exception $e) {
            $this->assertEquals('Offset '.$key.' does not exist.', $e->getMessage());
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testFieldGetter()
    {
        $dto = new ExampleDTO();

        $this->assertEquals(true, $dto->foo);
    }

    public function testFieldSetter()
    {
        $dto = new ExampleDTO();

        $dto->foo = 'foo';

        $isSet = isset($dto->foo);

        $this->assertTrue($isSet);
        $this->assertEquals('foo', $dto->foo);
    }

    public function testToString()
    {
        $dto = new ExampleDTO();

        $this->assertEquals('{"foo":true,"bar":"string","extra":{"a":"b"}}', (string)$dto);
    }

    public function testToStringWithCustomSerializer()
    {
        $dto = new ExampleDTO();
        $dto->setSerializer(new CustomSerializer());

        $this->assertEquals('a:3:{s:3:"foo";b:1;s:3:"bar";s:6:"string";s:5:"extra";a:1:{s:1:"a";s:1:"b";}}', (string)$dto);
    }

    public function testCanIterateDTO()
    {
        $dto = new ExampleDTO();

        $expectedArray = ['foo' => true, 'bar' => 'string', 'extra' => ['a' => 'b']];
        $gotArray = [];

        foreach ($dto as $key => $value) {
            $gotArray[$key] = $value;
        }

        $this->assertEquals($expectedArray, $gotArray);
    }

    public function testIfSerializerSetFromConstructor()
    {
        $dto = new ExampleDTO([], $this->getMockBuilder(DTOSerializerInterface::class)->getMock());

        $this->assertInstanceOf(
            DTOSerializerInterface::class,
            $dto->getSerializer(),
            'Serializer must implement DTOSerializerInterface'
        );
    }

    public function testIfSerializerNotSetFromConstructor()
    {
        $dto = new ExampleDTO();

        $this->assertInstanceOf(JsonSerializer::class, $dto->getSerializer(), 'Default serializer must be JsonSerializer');
    }

    public function testGetNonExistentKeyInChain()
    {
        $dto = new ExampleDTO();

        $key = 'non.Existent.Key';

        try {
            $dto->get($key);
        } catch (\Exception $e) {
            $this->assertEquals('Non existent offset given in offset chain: non', $e->getMessage());
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testGetKeyInChainOfArray()
    {
        $dto = new ExampleDTO(['foo' => ['b' => ['c' => ['d' => 'foo']]]]);

        $this->assertInstanceOf(\ArrayAccess::class, $dto);

        $this->assertEquals('foo', $dto->get('foo.b.c.d'));
        $this->assertEquals('foo', $dto['foo']['b']['c']['d']);
        $this->assertEquals('foo', $dto['foo.b.c.d']);
        $this->assertEquals(['d' => 'foo'], $dto->get('foo.b.c'));
    }

    public function testGetKeyInChainOfObject()
    {
        $dto = new ExampleDTO(json_encode(['foo' => ['b' => ['c' => ['d' => 'foo']]]]));

        $this->assertInstanceOf(\ArrayAccess::class, $dto);

        $this->assertEquals('foo', $dto->get('foo.b.c.d'));
        $this->assertEquals('foo', $dto['foo']->b->c->d);
        $this->assertEquals('foo', $dto->foo->b->c->d);
        $this->assertEquals('foo', $dto['foo.b.c.d']);
        $this->assertEquals(json_decode('{"d":"foo"}'), $dto->get('foo.b.c'));
    }
}