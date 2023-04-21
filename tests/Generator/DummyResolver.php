<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

final class DummyResolver
{
    public function __construct(private readonly string $prefix = '')
    {
    }

    public function __invoke(DummyGeneratedClass $input)
    {
        return $this->prefix . $input->data['output'];
    }
}
