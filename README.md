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
`CodinPro\DataTransferObject\DTO` class and
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

After this *"painful"* setup, you are ready to go. You can find more usage examples on [Wiki](https://github.com/CodinPro/data-transfer-object/wiki/Usage).


Contribution
============
Feel free to create your pull requests. The only requirements are:
1) Keep code quality at 10/10
2) Keep code coverage at 100%
3) Don't brake back-compatibility
