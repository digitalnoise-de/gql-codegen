<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

/**
 * @psalm-immutable
 */
final class GeneratedClassType implements Type
{
    public string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }
}
