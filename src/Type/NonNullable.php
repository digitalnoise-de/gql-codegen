<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class NonNullable implements Type
{
    public ScalarType|GeneratedClassType|ExistingClassType|ListType $elementType;

    public function __construct(ScalarType|GeneratedClassType|ExistingClassType|ListType $elementType)
    {
        $this->elementType = $elementType;
    }

    public function __toString(): string
    {
        return sprintf('NonNullable<%s>', (string)$this->elementType);
    }
}
