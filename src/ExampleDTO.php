<?php

namespace CodinPro\DataTransferObject;

class ExampleDTO extends DTO
{
    protected bool $foo = true;
    protected string $bar = 'string';
    protected array $extra = ['a' => 'b'];
    protected mixed $some = null;
}