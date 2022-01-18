<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class ListType implements Type
{
    public NonNullable|ScalarType|GeneratedClassType|ExistingClassType $elementType;

    public function __construct(NonNullable|ScalarType|GeneratedClassType|ExistingClassType $elementType)
    {
        $this->elementType = $elementType;
    }

    public function __toString(): string
    {
        return sprintf('List<%s>', (string)$this->elementType);
    }
}
