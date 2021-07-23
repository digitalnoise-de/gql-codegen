<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\MainResolverDefinition;

interface MainResolverClassGenerator
{
    public function generate(MainResolverDefinition $definition): GeneratedClass;
}