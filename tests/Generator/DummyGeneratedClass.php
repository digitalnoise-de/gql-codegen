<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

final class DummyGeneratedClass
{
    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
