<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\Type\ScalarType;

abstract class MainResolverClassGeneratorTest extends ClassGeneratorTestCase
{
    /**
     * @test
     */
    public function resolver_should_be_executed(): void
    {
        $className  = $this->randomClassName();
        $definition = new MainResolverDefinition(
            $className,
            [
                $this->dummyResolverDefinition('Query', 'foo'),
            ]
        );

        $this->generateAndEvaluate($definition);

        $resolver     = new DummyResolver();
        $mainResolver = new $className($resolver);

        $result = $mainResolver->resolve('Query', 'foo', null, ['output' => 'Hello']);

        self::assertSame('Hello', $result);
    }

    private function dummyResolverDefinition(string $type, string $field): ResolverDefinition
    {
        return new ResolverDefinition(
            DummyResolver::class,
            $type,
            $field,
            null,
            new InputTypeDefinition(DummyGeneratedClass::class, ['output' => ScalarType::STRING()]),
            ScalarType::STRING()
        );
    }

    private function generateAndEvaluate(MainResolverDefinition $definition): void
    {
        $generatedClass = $this->subject()->generate($definition);

        $code = str_replace('<?php', '', $generatedClass->content);

        eval($code);
    }

    abstract protected function subject(): MainResolverClassGenerator;

    /**
     * @test
     */
    public function fields_with_the_same_name_of_different_types_should_be_resolved_properly(): void
    {
        $className  = $this->randomClassName();
        $definition = new MainResolverDefinition(
            $className,
            [
                $this->dummyResolverDefinition('A', 'foo'),
                $this->dummyResolverDefinition('B', 'foo'),
            ]
        );

        $this->generateAndEvaluate($definition);

        $mainResolver = new $className(new DummyResolver('A: '), new DummyResolver('B: '));

        self::assertSame('A: Hello', $mainResolver->resolve('A', 'foo', null, ['output' => 'Hello']));
        self::assertSame('B: Hello', $mainResolver->resolve('B', 'foo', null, ['output' => 'Hello']));
    }

    /**
     * @test
     */
    public function canResolve_should_return_whether_a_field_is_resolvable(): void
    {
        $className  = $this->randomClassName();
        $definition = new MainResolverDefinition(
            $className,
            [
                $this->dummyResolverDefinition('A', 'foo'),
                $this->dummyResolverDefinition('B', 'foo'),
            ]
        );

        $this->generateAndEvaluate($definition);

        $mainResolver = new $className(new DummyResolver('A: '), new DummyResolver('B: '));

        self::assertTrue($mainResolver->canResolve('A', 'foo'), 'A.foo should be resolvable');
        self::assertFalse($mainResolver->canResolve('A', 'bar'), 'A.bar should not be resolvable');
        self::assertTrue($mainResolver->canResolve('B', 'foo'), 'B.foo should be resolvable');
        self::assertFalse($mainResolver->canResolve('C', 'foo'), 'C.foo should not be resolvable');
    }
}
