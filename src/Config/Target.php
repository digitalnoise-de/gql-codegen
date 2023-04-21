<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

/**
 * @psalm-immutable
 */
final class Target
{
    public function __construct(public readonly string $namespacePrefix, public readonly string $directory)
    {
    }
}
