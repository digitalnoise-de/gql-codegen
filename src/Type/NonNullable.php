<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class NonNullable implements Type, \Stringable
{
    public function __construct(public readonly ScalarType|GeneratedClassType|ExistingClassType|ListType $elementType)
    {
    }

    public function __toString(): string
    {
        return sprintf('NonNullable<%s>', (string)$this->elementType);
    }
}
