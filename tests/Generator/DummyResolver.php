<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

final class DummyResolver
{
    private string $prefix;

    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    public function __invoke(DummyGeneratedClass $input)
    {
        return $this->prefix.$input->data['output'];
    }
}
