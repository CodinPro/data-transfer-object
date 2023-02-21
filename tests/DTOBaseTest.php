<?php

namespace CodinPro\Tests\DataTransferObject;

use CodinPro\DataTransferObject\DTOBase;
use CodinPro\DataTransferObject\DTOSerializerInterface;
use CodinPro\DataTransferObject\ExampleDTO;
use CodinPro\DataTransferObject\JsonSerializer;
use Exception;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\TestCase;

class DTOBaseTest extends TestCase
{
    private static array $initialData = [
        'test1' => 'one',
        'test3' => 'three',
    ];
    private static string $initialJsonData = '{"test1":"one","test3":"three"}';
    private static array $initialDefaultData = [
        'test1' => '1',
        'test2' => '2',
        'test3' => '3',
    ];

    /**
     * @throws JsonException
     */
    public function testBuildFromArray(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals('2', $dto->get('test2'));
        $this->assertEquals('three', $dto->get('test3'));
    }

    public function testBuildFromInvalidDataType(): void
    {
        try {
            $dto = new DTOBase(self::$initialDefaultData, 123);
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

    /**
     * @throws JsonException
     */
    public function testBuildFromJson(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialJsonData);

        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals('2', $dto->get('test2'));
        $this->assertEquals('three', $dto->get('test3'));
    }

    /**
     * @throws JsonException
     */
    public function testBuildFromObject(): void
    {
        $object = json_decode(self::$initialJsonData, false, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals('object', gettype($object));

        $dto = new DTOBase(self::$initialDefaultData, $object);

        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals('2', $dto->get('test2'));
        $this->assertEquals('three', $dto->get('test3'));
    }

    /**
     * @throws JsonException
     */
    public function testCanIterateDTO(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $expectedArray = ['test1' => 'one', 'test2' => '2', 'test3' => 'three'];
        $gotArray = [];

        foreach ($dto as $key => $value) {
            $gotArray[$key] = $value;
        }

        $this->assertEquals($expectedArray, $gotArray);
    }

    /**
     * @throws JsonException
     */
    public function testCount(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertCount(count(array_keys(self::$initialDefaultData)), $dto);
    }

    /**
     * @throws JsonException
     */
    public function testFieldGetter(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals('one', $dto->test1);
        $this->assertEquals('one', $dto->get('test1'));
        $this->assertEquals(null, $dto->get('test10000'));
    }

    /**
     * @throws JsonException
     */
    public function testFieldSetter(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $dto->test1 = 'test1';

        $isSet = isset($dto->test1);

        $this->assertTrue($isSet);
        $this->assertEquals('test1', $dto->test1);
    }

    /**
     * @throws JsonException
     */
    public function testGetKeyInChain(): void
    {
        $dto = new DTOBase(['a' => ['b' => ['c' => ['d' => 'foo']]]]);
        $dto2 = new DTOBase(['a' => ['b' => ['c' => ['d' => false]]]], json_encode(['a' => ['b' => ['c' => ['d' => 'foo']]]], JSON_THROW_ON_ERROR));

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

    /**
     * @throws JsonException
     */
    public function testGetNonExistentKey(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $key = 'nonExistentKey';

        try {
            $dto->get($key, true);
        } catch (Exception $e) {
            $this->assertEquals('Offset ' . $key . ' does not exist.', $e->getMessage());
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }
    }

    /**
     * @throws JsonException
     */
    public function testGetNonExistentKeyInChain(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $key = 'non.Existent.Key';

        try {
            $dto->get($key);
        } catch (Exception $e) {
            $this->assertEquals('Non existent offset given in offset chain: non', $e->getMessage());
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
        }
    }

    /**
     * @throws JsonException
     */
    public function testIfSerializerNotSetFromConstructor(): void
    {
        $dto = new DTOBase([], []);

        $this->assertInstanceOf(JsonSerializer::class, $dto->getSerializer(), 'Default serializer must be JsonSerializer');
    }

    /**
     * @throws JsonException
     */
    public function testIfSerializerSetFromConstructor(): void
    {
        $dto = new DTOBase([], [], $this->getMockBuilder(DTOSerializerInterface::class)->getMock());

        $this->assertInstanceOf(
            DTOSerializerInterface::class,
            $dto->getSerializer(),
            'Serializer must implement DTOSerializerInterface'
        );
    }

    /**
     * @throws JsonException
     */
    public function testOffsetExists(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertNotEmpty($dto['test1']);
        $this->assertTrue(isset($dto['test1']));
    }

    /**
     * @throws JsonException
     */
    public function testSetValue(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);
        $dto['test4'] = 'four';

        $this->assertEquals('four', $dto->get('test4'));
    }

    /**
     * @throws JsonException
     */
    public function testToArrayWithCustomSerializer(): void
    {
        $array = ['a' => ['b' => ['c' => ['d' => 'foo']]]];
        $dto = new DTOBase($array, [], $this->getMockBuilder(DTOSerializerInterface::class)->getMock());

        $this->assertEquals($array, $dto->toArray());
    }

    /**
     * @throws JsonException
     */
    public function testToArrayWithDefaultSerializer(): void
    {
        $array = ['a' => ['b' => ['c' => ['d' => 'foo']]]];
        $dto = new DTOBase(['a' => ['b' => ['c' => ['d' => 'foo']]]]);

        $this->assertEquals($array, $dto->toArray());
    }

    /**
     * @throws JsonException
     */
    public function testToString(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        $this->assertEquals('{"test1":"one","test2":"2","test3":"three"}', (string) $dto);
    }

    /**
     * @throws JsonException
     */
    public function testUnset(): void
    {
        $dto = new DTOBase(self::$initialDefaultData, self::$initialData);

        unset($dto['test1']);

        $this->assertEquals('1', $dto->get('test1'));
    }
}
