<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class ExistingClassType implements ConcreteType, \Stringable
{
    public function __construct(public readonly string $className)
    {
    }

    public function __toString(): string
    {
        return sprintf('ExistingClass<%s>', $this->className);
    }
}
