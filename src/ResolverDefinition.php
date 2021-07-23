<?php
declare(strict_types=1);

namespace GraphQLGenerator;

use GraphQLGenerator\Type\Type;

final class ResolverDefinition
{
    public string $className;

    public string $typeName;

    public string $fieldName;

    public ?string $valueType;

    public ?string $argsClassName;

    public Type $returnType;

    public function __construct(
        string $className,
        string $typeName,
        string $fieldName,
        ?string $valueType,
        ?string $argsClassName,
        Type $returnType
    ) {
        $this->className     = $className;
        $this->typeName      = $typeName;
        $this->fieldName     = $fieldName;
        $this->valueType     = $valueType;
        $this->argsClassName = $argsClassName;
        $this->returnType    = $returnType;
    }
}
