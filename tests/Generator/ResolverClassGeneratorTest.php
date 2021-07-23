<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use GraphQLGenerator\Generator\ResolverClassGenerator;
use GraphQLGenerator\ResolverDefinition;
use GraphQLGenerator\Type\Scalar;

abstract class ResolverClassGeneratorTest extends ClassGeneratorTestCase
{
    /**
     * @test
     */
    public function it_should_generate_the_class(): void
    {
        $className  = $this->randomClassName();
        $definition = new ResolverDefinition($className, 'Type', 'field', null, null, Scalar::STRING());

        $this->generateAndEvaluate($definition);

        self::assertTrue(class_exists($className));
    }

    private function generateAndEvaluate(ResolverDefinition $definition): void
    {
        $generatedClass = $this->subject()->generate($definition);

        $code = str_replace('<?php', '', $generatedClass->content);

        eval($code);
    }

    abstract protected function subject(): ResolverClassGenerator;
}