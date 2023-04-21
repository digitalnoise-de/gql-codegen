<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

final class GeneratedClass
{
    public function __construct(public readonly string $name, public readonly string $content)
    {
    }
}
