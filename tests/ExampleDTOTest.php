<?php

namespace CodinPro\Tests\DataTransferObject;

use ArrayAccess;
use CodinPro\DataTransferObject\CustomSerializer;
use CodinPro\DataTransferObject\DTOSerializerInterface;
use CodinPro\DataTransferObject\ExampleDTO;
use CodinPro\DataTransferObject\JsonSerializer;
use Exception;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\TestCase;

class ExampleDTOTest extends TestCase
{
    public function testBuildFromArray(): void
    {
        $dto = new ExampleDTO();

        $this->assertEquals(true, $dto->get('foo'));
        $this->assertEquals('string', $dto->get('bar'));
        $this->assertEquals(['a' => 'b'], $dto->get('extra'));
        $this->assertEquals(null, $dto->get('some'));
    }

    public function testBuildFromInvalidDataType(): void
    {
        try {
            $dto = new ExampleDTO(123);
        } catch (Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertEquals(
                'DTO can be built from array|object|json, "integer" given.',
                $e->getMessage()
            );
        }

        $this->assertFalse(isset($dto));
    }

    public function testBuildFromInvalidJson(): void
    {
        try {
            $dto = new ExampleDTO('"test1":"one","test3":"three"');
        } catch (Exception $e) {
            $this->assertEquals('Syntax error', $e->getMessage());
        }

        $this->assertFalse(isset($dto));
    }

    public function testBuildFromJson(): void
    {
        $dto = new ExampleDTO('{"foo":false,"extra":"some extra value"}');

        $this->assertEquals(false, $dto->get('foo'));
        $this->assertEquals('string', $dto->get('bar'));
        $this->assertEquals('some extra value', $dto->get('extra'));
    }

    /**
     * @throws JsonException
     */
    public function testBuildFromObject(): void
    {
        $object = json_decode('{"foo":false,"extra":"some extra value"}', false, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('object', gettype($object));

        $dto = new ExampleDTO($object);

        $this->assertEquals(false, $dto->get('foo'));
        $this->assertEquals('string', $dto->get('bar'));
        $this->assertEquals('some extra value', $dto->get('extra'));
    }

    public function testCanIterateDTO(): void
    {
        $dto = new ExampleDTO();

        $expectedArray = ['foo' => true, 'bar' => 'string', 'extra' => ['a' => 'b'], 'some' => null];
        $gotArray = [];

        foreach ($dto as $key => $value) {
            $gotArray[$key] = $value;
        }

        $this->assertEquals($expectedArray, $gotArray);
    }

    public function testCount(): void
    {
        $dto = new ExampleDTO();

        $this->assertCount(4, $dto);
    }

    public function testFieldGetter(): void
    {
        $dto = new ExampleDTO();

        $this->assertEquals(true, $dto->foo);
    }

    public function testFieldSetter(): void
    {
        $dto = new ExampleDTO();

        $dto->foo = 'foo';

        $isSet = isset($dto->foo);

        $this->assertTrue($isSet);
        $this->assertEquals('foo', $dto->foo);
    }

    public function testGetKeyInChainOfArray(): void
    {
        $dto = new ExampleDTO(['foo' => ['b' => ['c' => ['d' => 'foo']]]]);

        $this->assertInstanceOf(ArrayAccess::class, $dto);

        $this->assertEquals('foo', $dto->get('foo.b.c.d'));
        $this->assertEquals('foo', $dto['foo']['b']['c']['d']);
        $this->assertEquals('foo', $dto['foo.b.c.d']);
        $this->assertEquals(['d' => 'foo'], $dto->get('foo.b.c'));
    }

    /**
     * @throws JsonException
     */
    public function testGetKeyInChainOfObject(): void
    {
        $dto = new ExampleDTO(json_encode(['foo' => ['b' => ['c' => ['d' => 'foo']]]], JSON_THROW_ON_ERROR));

        $this->assertInstanceOf(ArrayAccess::class, $dto);

        $this->assertEquals('foo', $dto->get('foo.b.c.d'));
        $this->assertEquals('foo', $dto['foo']->b->c->d);
        $this->assertEquals('foo', $dto->foo->b->c->d);
        $this->assertEquals('foo', $dto['foo.b.c.d']);
        $this->assertEquals(json_decode('{"d":"foo"}', false, 512, JSON_THROW_ON_ERROR), $dto->get('foo.b.c'));
    }

    public function testGetNonExistentKey(): void
    {
        $dto = new ExampleDTO();

        $key = 'nonExistentKey';

        try {
            $dto->get($key, true);
        } catch (Exception $e) {
            $this->assertEquals('Offset ' . $key . ' does not exist.', $e->getMessage());
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }
    }

    public function testGetNonExistentKeyInChain(): void
    {
        $dto = new ExampleDTO();

        $key = 'non.Existent.Key';

        try {
            $dto->get($key);
        } catch (Exception $e) {
            $this->assertEquals('Non existent offset given in offset chain: non', $e->getMessage());
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }
    }

    public function testIfSerializerNotSetFromConstructor(): void
    {
        $dto = new ExampleDTO();

        $this->assertInstanceOf(JsonSerializer::class, $dto->getSerializer(), 'Default serializer must be JsonSerializer');
    }

    public function testIfSerializerSetFromConstructor(): void
    {
        $dto = new ExampleDTO([], $this->getMockBuilder(DTOSerializerInterface::class)->getMock());

        $this->assertInstanceOf(
            DTOSerializerInterface::class,
            $dto->getSerializer(),
            'Serializer must implement DTOSerializerInterface'
        );
    }

    public function testInternalDataNamingConflict(): void
    {
        try {
            $dto = new ExampleDTO(['internalDTOData' => 'test']);
        } catch (Exception $e) {
            $this->assertEquals('internalDTO* fields are restricted', $e->getMessage());
        }
    }

    public function testInternalDefaultNamingConflict(): void
    {
        try {
            $dto = new ExampleDTO(['internalDTODefault' => 'test']);
        } catch (Exception $e) {
            $this->assertEquals('internalDTO* fields are restricted', $e->getMessage());
        }
    }

    public function testOffsetExists(): void
    {
        $dto = new ExampleDTO();

        $this->assertNotEmpty($dto['foo']);
        $this->assertTrue(isset($dto['foo']));
    }

    public function testSetValue(): void
    {
        $dto = new ExampleDTO();
        $dto['test4'] = 'four';

        $this->assertEquals('four', $dto->get('test4'));
    }

    public function testToString(): void
    {
        $dto = new ExampleDTO();

        $this->assertEquals('{"foo":true,"bar":"string","extra":{"a":"b"},"some":null}', (string) $dto);
    }

    public function testToStringWithCustomSerializer(): void
    {
        $dto = new ExampleDTO();
        $dto->setSerializer(new CustomSerializer());

        $this->assertEquals('a:4:{s:3:"foo";b:1;s:3:"bar";s:6:"string";s:5:"extra";a:1:{s:1:"a";s:1:"b";}s:4:"some";N;}', (string) $dto);
    }

    public function testUnset(): void
    {
        $dto = new ExampleDTO(['foo' => false]);

        $this->assertEquals(false, $dto->get('foo'));

        unset($dto['foo']);

        $this->assertEquals(true, $dto->get('foo'));
    }
}
