<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

use GraphQLGenerator\Type\Type;

final class InputTypeDefinition
{
    public string $className;

    /**
     * @var array<string, Type>
     */
    public array $fields;

    /**
     * @param array<string, Type> $fields
     */
    public function __construct(string $className, array $fields)
    {
        $this->className = $className;
        $this->fields    = $fields;
    }
}
