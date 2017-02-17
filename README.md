Data Transfer Object
====================
Common library for DTOs

[![Latest Stable Version](https://poser.pugx.org/codin-pro/data-transfer-object/version)](https://packagist.org/packages/codin-pro/data-transfer-object)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CodinPro/data-transfer-object/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/CodinPro/data-transfer-object/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/CodinPro/data-transfer-object/badges/build.png?b=master)](https://scrutinizer-ci.com/g/CodinPro/data-transfer-object/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/CodinPro/data-transfer-object/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/CodinPro/data-transfer-object/?branch=master)
[![Total Downloads](https://poser.pugx.org/codin-pro/data-transfer-object/downloads)](https://packagist.org/packages/codin-pro/data-transfer-object)
[![License](https://poser.pugx.org/codin-pro/data-transfer-object/license)](https://packagist.org/packages/codin-pro/data-transfer-object)
[![composer.lock available](https://poser.pugx.org/codin-pro/data-transfer-object/composerlock)](https://packagist.org/packages/codin-pro/data-transfer-object)


Install
=======
`composer require codin-pro/data-transfer-object`

or

```yaml
    "require": {
        "codin-pro/data-transfer-object": "^1.0"
    }
```

Usage
=====
All you need for your custom DTO is extending from 
CodinPro\DataTransferObject\DTO class and
defining fields as `protected`. That's it!
```php
<?php

namespace CodinPro\DataTransferObject;

class ExampleDTO extends DTO
{
    protected $foo = true;
    protected $bar = 'string';
    protected $extra = ['a' => 'b'];
}
```

After this *"painful"* setup, you can use fresh-made DTO like follows:

#####Without providing data (uses default values from your DTO)
```php
$dto = new ExampleDTO();
$dto->foo; // true
$dto->bar; // "string"
$dto->extra; // ["a" => "b"]
```

#####Initialize with array/object/json
```php
// its allowed to init from array, object or json
$dto = new ExampleDTO('{"extra":"some extra value"}');
$dto->foo; // true
$dto->bar; // "string"
$dto->extra; // "some extra value"
```

#####Set value
```php
$dto = new ExampleDTO();
$dto->foo = 'baz'; // "baz"
$dto['bar'] = 'test'; // "test"
```

#####Unset value
```php
$dto = new ExampleDTO(['foo' => "baz"]);
$dto->foo; // "baz"
unset($dto->foo); // removes current value of "foo", getting back to default value "true"
$dto->foo; // true
$dto->bar; // "string"
$dto->extra; // ["a" => "b"]
```

#####Nested getter
```php
$dto = new ExampleDTO(['extra' => ['a' => ['b' => ['c' => 'd']]]]);
$dto->foo; // "baz"
unset($dto->foo); // removes current value of "foo", getting back to default value "true"
$dto->foo; // true
$dto->bar; // "string"
$dto->extra; // ['a' => ['b' => ['c' => 'd']]]
$dto['extra']; // ['a' => ['b' => ['c' => 'd']]]
$dto->get('extra'); // ['a' => ['b' => ['c' => 'd']]]
$dto->get('extra.a'); // ['b' => ['c' => 'd']]
$dto->get('extra.a.b'); // ['c' => 'd']
$dto->get('extra.a.b.c'); // "d"
```

#####Serializer
```php
$dto = new ExampleDTO();
// when trying to convert to string, it calls inner $this->serialize();
(string)$dto; // {"foo":true,"bar":"string","extra":{"a":"b"}}
```
By default, it's using built-in JsonSerializer, 
but you can implement from DTOSerializerInterface and make your own.
```php
$dto->setSerializer($serializer);
// or as second param in DTO constructor
$dto = new ExampleDTO([], $serializer);
```
