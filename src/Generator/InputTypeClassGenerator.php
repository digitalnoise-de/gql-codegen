<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\Build\InputTypeDefinition;

interface InputTypeClassGenerator
{
    public function generate(InputTypeDefinition $definition): GeneratedClass;
}
