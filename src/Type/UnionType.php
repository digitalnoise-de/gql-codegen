<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

/**
 * @psalm-immutable
 */
final class UnionType implements ConcreteType
{
    /**
     * @param list<ExistingClassType|GeneratedClassType> $types
     */
    public function __construct(public readonly array $types)
    {
    }

    public function __toString(): string
    {
        return 'UnionType';
    }
}
