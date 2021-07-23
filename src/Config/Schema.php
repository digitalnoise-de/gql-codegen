<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

/**
 * @psalm-immutable
 */
final class Schema
{
    /**
     * @var list<string>
     */
    public array $files;

    /**
     * @param list<string> $files
     */
    public function __construct(array $files)
    {
        $this->files = $files;
    }
}
