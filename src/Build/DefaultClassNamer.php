<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

final class DefaultClassNamer implements ClassNamer
{
    public function __construct(private readonly string $namespacePrefix)
    {
    }

    public function inputType(string $typeName): string
    {
        return sprintf('%s\\Type\\%s', $this->namespacePrefix, ucfirst($typeName));
    }

    public function argumentType(string $typeName, string $fieldName): string
    {
        return sprintf('%s\\Resolver\\%s\%sArguments', $this->namespacePrefix, ucfirst($typeName), ucfirst($fieldName));
    }

    public function resolver(string $typeName, string $fieldName): string
    {
        return sprintf('%s\\Resolver\\%s\\%sResolver', $this->namespacePrefix, ucfirst($typeName), ucfirst($fieldName));
    }

    public function mainResolver(): string
    {
        return sprintf('%s\\Resolver\\MainResolver', $this->namespacePrefix);
    }
}
