<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

/**
 * @psalm-immutable
 */
final class ExistingClassType implements ConcreteType
{
    public function __construct(public readonly string $className)
    {
    }

    public function __toString(): string
    {
        return sprintf('ExistingClass<%s>', $this->className);
    }
}
