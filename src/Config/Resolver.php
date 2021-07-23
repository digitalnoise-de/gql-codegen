<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

final class Resolver
{
    public string $type;

    public string $field;

    public function __construct(string $type, string $field)
    {
        $this->type  = $type;
        $this->field = $field;
    }
}
