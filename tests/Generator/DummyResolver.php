<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

final class DummyResolver
{
    public function __invoke(DummyGeneratedClass $input)
    {
        return $input->data['output'];
    }
}
