<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

/**
 * @psalm-immutable
 */
final class Target
{
    public string $namespacePrefix;

    public string $directory;

    public function __construct(string $namespacePrefix, string $directory)
    {
        $this->namespacePrefix = $namespacePrefix;
        $this->directory       = $directory;
    }
}
