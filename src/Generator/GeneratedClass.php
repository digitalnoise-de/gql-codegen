<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

final class GeneratedClass
{
    public string $name;

    public string $content;

    public function __construct(string $name, string $content)
    {
        $this->name    = $name;
        $this->content = $content;
    }
}
