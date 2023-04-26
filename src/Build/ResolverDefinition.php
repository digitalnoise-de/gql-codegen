<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

use GraphQLGenerator\Type\Type;

final class ResolverDefinition
{
    public function __construct(
        public readonly string $className,
        public readonly string $typeName,
        public readonly string $fieldName,
        public readonly ?Type $valueType,
        public readonly ?InputTypeDefinition $args,
        public readonly Type $returnType
    ) {
    }
}
