<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Generator\Php80\InputTypeClassGeneratorForPhp80;
use GraphQLGenerator\Generator\Php80\MainResolverClassGeneratorForPhp80;
use GraphQLGenerator\Generator\Php80\ResolverInterfaceGeneratorForPhp80;

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
            new InputTypeClassGeneratorForPhp80(),
            new ResolverInterfaceGeneratorForPhp80(),
            new MainResolverClassGeneratorForPhp80()
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
