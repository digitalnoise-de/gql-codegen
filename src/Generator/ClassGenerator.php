<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator;

use GraphQLGenerator\Generator\Php74\InputTypeClassGeneratorForPhp74;
use GraphQLGenerator\Generator\Php74\MainResolverClassGeneratorForPhp74;
use GraphQLGenerator\Generator\Php74\ResolverClassGeneratorForPhp74;
use GraphQLGenerator\InputTypeDefinition;
use GraphQLGenerator\MainResolverDefinition;
use GraphQLGenerator\ResolverDefinition;

final class ClassGenerator
{
    private InputTypeClassGenerator $inputTypeClassGenerator;

    private ResolverClassGenerator $resolverClassGenerator;

    private MainResolverClassGenerator $mainResolverClassGenerator;

    public function __construct(
        InputTypeClassGenerator $inputTypeClassGenerator,
        ResolverClassGenerator $resolverClassGenerator,
        MainResolverClassGenerator $mainResolverClassGenerator
    ) {
        $this->inputTypeClassGenerator    = $inputTypeClassGenerator;
        $this->resolverClassGenerator     = $resolverClassGenerator;
        $this->mainResolverClassGenerator = $mainResolverClassGenerator;
    }

    public static function forPhp74(): self
    {
        return new self(
            new InputTypeClassGeneratorForPhp74(),
            new ResolverClassGeneratorForPhp74(),
            new MainResolverClassGeneratorForPhp74()
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
