<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class NonNullable implements Type
{
    /**
     * @var Scalar|GeneratedClassType|ListType
     */
    public object $elementType;

    /**
     * @param Scalar|GeneratedClassType|ListType $elementType
     */
    public function __construct(object $elementType)
    {
        $this->elementType = $elementType;
    }

    public function __toString(): string
    {
        return sprintf('NonNullable<%s>', (string)$this->elementType);
    }
}
