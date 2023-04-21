<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

final class DummyGeneratedClass
{
    public function __construct(public readonly array $data)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
