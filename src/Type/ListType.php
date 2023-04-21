<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class ListType implements Type, \Stringable
{
    public function __construct(
        public readonly NonNullable|ScalarType|GeneratedClassType|ExistingClassType $elementType
    ) {
    }

    public function __toString(): string
    {
        return sprintf('List<%s>', (string)$this->elementType);
    }
}
