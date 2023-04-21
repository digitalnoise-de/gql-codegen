<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

use GraphQLGenerator\Type\Type;

final class InputTypeDefinition
{
    /**
     * @param array<string, Type> $fields
     */
    public function __construct(public readonly string $className, public readonly array $fields)
    {
    }
}
