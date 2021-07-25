<?php
declare(strict_types=1);

namespace GraphQLGenerator;

final class DefaultClassNamer implements ClassNamer
{
    private string $namespacePrefix;

    public function __construct(string $namespacePrefix)
    {
        $this->namespacePrefix = $namespacePrefix;
    }

    public function inputType(string $typeName): string
    {
        return sprintf('%s\\Type\\%s', $this->namespacePrefix, ucfirst($typeName));
    }

    public function argumentType(string $fieldName): string
    {
        return sprintf('%s\\Type\\%sArguments', $this->namespacePrefix, ucfirst($fieldName));
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
