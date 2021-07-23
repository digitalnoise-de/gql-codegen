<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class ListType implements Type
{
    /**
     * @var NonNullable|Scalar|GeneratedClassType
     */
    public object $elementType;

    /**
     * @param NonNullable|Scalar|GeneratedClassType $elementType
     */
    public function __construct(object $elementType)
    {
        $this->elementType = $elementType;
    }
}
