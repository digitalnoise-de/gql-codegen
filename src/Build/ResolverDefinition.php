<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

use GraphQLGenerator\Type\Type;

final class ResolverDefinition
{
    public string $className;

    public string $typeName;

    public string $fieldName;

    public ?Type $valueType;

    public ?InputTypeDefinition $args;

    public Type $returnType;

    public function __construct(
        string $className,
        string $typeName,
        string $fieldName,
        ?Type $valueType,
        ?InputTypeDefinition $args,
        Type $returnType
    ) {
        $this->className  = $className;
        $this->typeName   = $typeName;
        $this->fieldName  = $fieldName;
        $this->valueType  = $valueType;
        $this->args       = $args;
        $this->returnType = $returnType;
    }
}
