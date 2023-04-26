<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

/**
 * @psalm-immutable
 */
final class TypeDetails
{
    public function __construct(
        public readonly string $phpType,
        public readonly bool $nullable,
        public readonly ?string $docBlockType
    ) {
    }
}
