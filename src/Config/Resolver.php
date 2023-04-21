<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

final class Resolver
{
    public function __construct(public readonly string $type, public readonly string $field)
    {
    }
}
