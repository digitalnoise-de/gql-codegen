<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\Build\ResolverDefinition;

interface ResolverInterfaceGenerator
{
    public function generate(ResolverDefinition $resolver): GeneratedClass;
}
