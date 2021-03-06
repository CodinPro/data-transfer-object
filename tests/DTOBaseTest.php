<?php

namespace CodinPro\DataTransferObject;

class DTOTest extends \PHPUnit_Framework_TestCase
{
    private static $initialData = [
        'test1' => 'one',
        'test3' => 'three',
    ];
    private static $initialJsonData = '{"test1":"one","test3":"three"}';
    private static $initialDefaultData = [
        'test1' => '1',
        'test2' => '2',
        'test3' => '3',
    ];

    public function testBuildFromArray()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals('2', $dto->get('test2'));
        $this->assertEquals('three', $dto->get('test3'));
    }

    public function testBuildFromJson()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialJsonData);

        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals('2', $dto->get('test2'));
        $this->assertEquals('three', $dto->get('test3'));
    }

    public function testBuildFromObject()
    {
        $object = json_decode(self::$initialJsonData);

        $this->assertEquals('object', gettype($object));

        $dto = new DTOBase(self::$initialDefaultData, $object);

        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals('2', $dto->get('test2'));
        $this->assertEquals('three', $dto->get('test3'));
    }

    public function testBuildFromInvalidJson()
    {
        try {
            $dto = new DTOBase(self::$initialDefaultData, '"test1":"one","test3":"three"');
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
            $dto = new DTOBase(self::$initialDefaultData, 123);
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
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);
        $dto['test4'] = 'four';

        $this->assertEquals('four', $dto->get('test4'));
    }

    public function testCount()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals(count(array_keys(self::$initialDefaultData)), count($dto));
    }

    public function testOffsetExists()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertNotEmpty($dto['test1']);
        $this->assertTrue(isset($dto['test1']));
    }

    public function testUnset()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        unset($dto['test1']);

        $this->assertEquals('1', $dto->get('test1'));
    }

    public function testGetNonExistentKey()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $key = 'nonExistentKey';

        try {
            $dto->get($key);
        } catch (\Exception $e) {
            $this->assertEquals('Offset ' . $key . ' does not exist.', $e->getMessage());
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testFieldGetter()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals('one', $dto->test1);
    }

    public function testFieldSetter()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $dto->test1 = 'test1';

        $isSet = isset($dto->test1);

        $this->assertTrue($isSet);
        $this->assertEquals('test1', $dto->test1);
    }

    public function testToString()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals('{"test1":"one","test2":"2","test3":"three"}', (string)$dto);
    }

    public function testCanIterateDTO()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $expectedArray = ['test1' => 'one', 'test2' => '2', 'test3' => 'three'];
        $gotArray = [];

        foreach ($dto as $key => $value) {
            $gotArray[$key] = $value;
        }

        $this->assertEquals($expectedArray, $gotArray);
    }

    public function testIfSerializerSetFromConstructor()
    {
        $dto = new DTOBase([], [], $this->getMockBuilder(DTOSerializerInterface::class)->getMock());

        $this->assertInstanceOf(
            DTOSerializerInterface::class,
            $dto->getSerializer(),
            'Serializer must implement DTOSerializerInterface'
        );
    }

    public function testIfSerializerNotSetFromConstructor()
    {
        $dto = new DTOBase([], []);

        $this->assertInstanceOf(JsonSerializer::class, $dto->getSerializer(), 'Default serializer must be JsonSerializer');
    }

    public function testGetNonExistentKeyInChain()
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $key = 'non.Existent.Key';

        try {
            $dto->get($key);
        } catch (\Exception $e) {
            $this->assertEquals('Non existent offset given in offset chain: non', $e->getMessage());
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
        }
    }

    public function testGetKeyInChain()
    {
        $dto = new DTOBase(['a' => ['b' => ['c' => ['d' => 'foo']]]]);
        $dto2 = new DTOBase(['a' => ['b' => ['c' => ['d' => false]]]], json_encode(['a' => ['b' => ['c' => ['d' => 'foo']]]]));

        $_dto = new \stdClass();
        $_dto2 = new \stdClass();
        $_dto2->d = 'foo';
        $_dto->a = ['b' => ['c' => $_dto2]];
        $dto3 = new DTOBase(['a' => null], $_dto);

        $this->assertInstanceOf(\ArrayAccess::class, $dto);
        $this->assertEquals('foo', $dto->get('a.b.c.d'));
        $this->assertEquals('foo', $dto2->get('a.b.c.d'));
        $this->assertEquals('foo', $dto['a']['b']['c']['d']);
        $this->assertEquals('foo', $dto['a.b.c.d']);
        $this->assertEquals('foo', $dto2['a.b.c.d']);
        $this->assertEquals(['d' => 'foo'], $dto->get('a.b.c'));
        $this->assertEquals('foo', $dto3->get('a.b.c.d'));
    }

    public function testToArrayWithDefaultSerializer()
    {
        $array = ['a' => ['b' => ['c' => ['d' => 'foo']]]];
        $dto = new DTOBase(['a' => ['b' => ['c' => ['d' => 'foo']]]]);

        $this->assertEquals($array, $dto->toArray());
    }

    public function testToArrayWithCustomSerializer()
    {
        $array = ['a' => ['b' => ['c' => ['d' => 'foo']]]];
        $dto = new DTOBase($array, [], $this->getMockBuilder(DTOSerializerInterface::class)->getMock());

        $this->assertEquals($array, $dto->toArray());
    }
}
