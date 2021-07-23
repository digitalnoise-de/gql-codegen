<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

/**
 * @psalm-immutable
 */
final class TypeDetails
{
    public string $phpType;

    public bool $nullable;

    public ?string $docBlockType;

    public function __construct(string $phpType, bool $nullable, ?string $docBlockType)
    {
        $this->phpType      = $phpType;
        $this->nullable     = $nullable;
        $this->docBlockType = $docBlockType;
    }
}
