<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;

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
     *
     * @dataProvider valueCheckProvider
     */
    public function field_can_not_be_resolved_with_invalid_input_value(
        Type   $valueType,
        mixed  $input,
        string $expectedType
    ): void {
        $className          = $this->randomClassName();
        $resolverDefinition = new ResolverDefinition(
            DummyResolver::class,
            'A',
            'foo',
            $valueType,
            null,
            ScalarType::STRING()
        );
        $definition         = new MainResolverDefinition($className, [$resolverDefinition]);
        $this->generateAndEvaluate($definition);
        $mainResolver = new $className(new DummyResolver());

        $this->expectExceptionMessage(
            sprintf('A.foo expectes value of type "%s", got "%s"', $expectedType, gettype($input))
        );

        $mainResolver->resolve('A', 'foo', $input, []);
    }

    public function valueCheckProvider(): iterable
    {
        yield 'Class type with scalar value' => [
            new ExistingClassType(DummyValue::class),
            'bar',
            DummyValue::class
        ];

        yield 'Non nullable String with int value' => [
            new NonNullable(ScalarType::STRING()),
            1,
            'string'
        ];

        yield 'String with int value' => [
            ScalarType::STRING(),
            1,
            'string'
        ];

        yield 'Int with float value' => [
            ScalarType::INTEGER(),
            1.23,
            'int'
        ];
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
