<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

/**
 * @psalm-immutable
 */
final class UnionType implements ConcreteType
{
    public function __toString(): string
    {
        return 'UnionType';
    }
}
