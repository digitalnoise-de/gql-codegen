<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Generator\Php81\InputTypeClassGeneratorForPhp81;
use GraphQLGenerator\Generator\Php81\MainResolverClassGeneratorForPhp81;
use GraphQLGenerator\Generator\Php81\ResolverInterfaceGeneratorForPhp81;

final class ClassGenerator
{
    public function __construct(
        private readonly InputTypeClassGenerator $inputTypeClassGenerator,
        private readonly ResolverInterfaceGenerator $resolverClassGenerator,
        private readonly MainResolverClassGenerator $mainResolverClassGenerator
    ) {
    }

    public static function forPhp80(): self
    {
        return new self(
            new InputTypeClassGeneratorForPhp81(),
            new ResolverInterfaceGeneratorForPhp81(),
            new MainResolverClassGeneratorForPhp81()
        );
    }

    public function inputType(InputTypeDefinition $definition): GeneratedClass
    {
        return $this->inputTypeClassGenerator->generate($definition);
    }

    public function resolver(ResolverDefinition $definition): GeneratedClass
    {
        return $this->resolverClassGenerator->generate($definition);
    }

    public function mainResolver(MainResolverDefinition $definition): GeneratedClass
    {
        return $this->mainResolverClassGenerator->generate($definition);
    }
}
