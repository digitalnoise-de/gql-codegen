<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\InputTypeDefinition;
use GraphQLGenerator\MainResolverDefinition;
use GraphQLGenerator\ResolverDefinition;
use GraphQLGenerator\Type\Scalar;

abstract class MainResolverClassGeneratorTest extends ClassGeneratorTestCase
{
    /**
     * @test
     */
    public function yo(): void
    {
        $className  = $this->randomClassName();
        $definition = new MainResolverDefinition(
            $className,
            [
                new ResolverDefinition(
                    DummyResolver::class,
                    'Query',
                    'foo',
                    null,
                    new InputTypeDefinition(DummyGeneratedClass::class, ['output' => Scalar::STRING()]),
                    Scalar::STRING()
                )
            ]
        );

        $this->generateAndEvaluate($definition);

        $resolver     = new DummyResolver();
        $mainResolver = new $className($resolver);

        $result = $mainResolver->resolve('Query', 'foo', null, ['output' => 'Hello']);

        self::assertSame('Hello', $result);
    }

    private function generateAndEvaluate(MainResolverDefinition $definition): void
    {
        $generatedClass = $this->subject()->generate($definition);

        $code = str_replace('<?php', '', $generatedClass->content);

        eval($code);
    }

    abstract protected function subject(): MainResolverClassGenerator;
}
