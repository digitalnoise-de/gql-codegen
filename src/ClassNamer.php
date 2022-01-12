<?php
declare(strict_types=1);

namespace GraphQLGenerator;

interface ClassNamer
{
    public function inputType(string $typeName): string;

    public function argumentType(string $typeName, string $fieldName): string;

    public function resolver(string $typeName, string $fieldName): string;

    public function mainResolver(): string;
}
