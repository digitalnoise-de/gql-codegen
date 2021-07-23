<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\ResolverDefinition;

interface ResolverClassGenerator
{
    public function generate(ResolverDefinition $resolver): GeneratedClass;
}
