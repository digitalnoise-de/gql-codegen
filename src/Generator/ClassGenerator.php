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
    private InputTypeClassGenerator $inputTypeClassGenerator;

    private ResolverInterfaceGenerator $resolverClassGenerator;

    private MainResolverClassGenerator $mainResolverClassGenerator;

    public function __construct(
        InputTypeClassGenerator $inputTypeClassGenerator,
        ResolverInterfaceGenerator $resolverClassGenerator,
        MainResolverClassGenerator $mainResolverClassGenerator
    ) {
        $this->inputTypeClassGenerator    = $inputTypeClassGenerator;
        $this->resolverClassGenerator     = $resolverClassGenerator;
        $this->mainResolverClassGenerator = $mainResolverClassGenerator;
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
