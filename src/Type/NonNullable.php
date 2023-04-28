<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

/**
 * @psalm-immutable
 */
final class NonNullable implements WrappingType
{
    public function __construct(public readonly ListType|ConcreteType $elementType)
    {
    }

    public static function fromType(Type $type): self
    {
        if ($type instanceof ListType || $type instanceof ConcreteType) {
            return new self($type);
        }

        throw new \RuntimeException(sprintf('Expected %s or %s, got %s', ListType::class, ConcreteType::class, $type::class));
    }

    public function __toString(): string
    {
        return sprintf('NonNullable<%s>', (string)$this->elementType);
    }
}
