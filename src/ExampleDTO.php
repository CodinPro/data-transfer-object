<?php

namespace CodinPro\DataTransferObject;

class ExampleDTO extends DTO
{
    protected $foo = true;
    protected $bar = 'string';
    protected $extra = ['a' => 'b'];
    protected $some;
}