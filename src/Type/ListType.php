<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

/**
 * @psalm-immutable
 */
final class ListType implements WrappingType
{
    public function __construct(public readonly NonNullable|ConcreteType $elementType)
    {
    }

    public static function fromType(Type $type): self
    {
        if ($type instanceof NonNullable || $type instanceof ConcreteType) {
            return new self($type);
        }

        throw new \RuntimeException(sprintf('Expected %s or %s, got %s', NonNullable::class, ConcreteType::class, $type::class));
    }

    public function __toString(): string
    {
        return sprintf('List<%s>', (string)$this->elementType);
    }
}
