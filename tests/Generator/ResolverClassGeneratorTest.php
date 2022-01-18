<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Generator\ResolverInterfaceGenerator;
use GraphQLGenerator\Type\ScalarType;

abstract class ResolverClassGeneratorTest extends ClassGeneratorTestCase
{
    /**
     * @test
     */
    public function it_should_generate_the_interface(): void
    {
        $className  = $this->randomClassName();
        $definition = new ResolverDefinition($className, 'Type', 'field', null, null, ScalarType::STRING());

        $this->generateAndEvaluate($definition);

        self::assertTrue(interface_exists($className));
    }

    private function generateAndEvaluate(ResolverDefinition $definition): void
    {
        $generatedClass = $this->subject()->generate($definition);

        $code = str_replace('<?php', '', $generatedClass->content);

        eval($code);
    }

    abstract protected function subject(): ResolverInterfaceGenerator;
}
