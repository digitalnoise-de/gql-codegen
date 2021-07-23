<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use Generator;
use GraphQLGenerator\Generator\InputTypeClassGenerator;
use GraphQLGenerator\InputTypeDefinition;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\Scalar;

abstract class InputTypeClassGeneratorTest extends ClassGeneratorTestCase
{
    /**
     * @test
     */
    public function it_should_generate_the_class(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, []);

        $this->generateAndEvaluate($definition);

        self::assertTrue(class_exists($className));
    }

    protected function generateAndEvaluate(InputTypeDefinition $definition): void
    {
        $generatedClass = $this->subject()->generate($definition);

        $code = str_replace('<?php', '', $generatedClass->content);

        eval($code);
    }

    abstract protected function subject(): InputTypeClassGenerator;

    /**
     * @test
     */
    public function generated_class_should_only_have_a_constructor_and_a_fromArray_method(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, ['names' => new ListType(Scalar::STRING())]);

        $this->generateAndEvaluate($definition);

        self::assertClassHasPublicMethods(['__construct', 'fromArray'], $className);
    }

    /**
     * @test
     */
    public function generated_class_should_have_a_public_property_for_each_field(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['firstName' => Scalar::STRING(), 'lastName' => Scalar::STRING()]
        );

        $this->generateAndEvaluate($definition);

        self::assertClassHasPublicProperty('firstName', $className);
        self::assertClassHasPublicProperty('lastName', $className);
    }

    /**
     * @test
     *
     * @dataProvider types
     */
    public function properties_should_be_typed(object $typeDefinition, string $expectedType): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, ['prop' => $typeDefinition]);

        $this->generateAndEvaluate($definition);

        self::assertPropertyHasType($expectedType, $className, 'prop');
    }

    public function types(): Generator
    {
        yield 'Non-nullable String' => [
            new NonNullable(Scalar::STRING()),
            'string'
        ];

        yield 'Non-nullable Boolean' => [
            new NonNullable(Scalar::BOOLEAN()),
            'bool'
        ];

        yield 'Non-nullable Integer' => [
            new NonNullable(Scalar::INTEGER()),
            'int'
        ];

        yield 'Non-nullable Float' => [
            new NonNullable(Scalar::FLOAT()),
            'float'
        ];

        yield 'Non-nullable List' => [
            new NonNullable(new ListType(Scalar::STRING())),
            'array'
        ];

        yield 'Non-nullable Generated class' => [
            new NonNullable(new GeneratedClassType(DummyGeneratedClass::class)),
            DummyGeneratedClass::class
        ];

        yield 'Nullable String' => [
            Scalar::STRING(),
            '?string'
        ];

        yield 'Nullable Boolean' => [
            Scalar::BOOLEAN(),
            '?bool'
        ];

        yield 'Nullable Integer' => [
            Scalar::INTEGER(),
            '?int'
        ];

        yield 'Nullable Float' => [
            Scalar::FLOAT(),
            '?float'
        ];

        yield 'Nullable List' => [
            new ListType(Scalar::STRING()),
            '?array'
        ];

        yield 'Nullable Generated class' => [
            new GeneratedClassType(DummyGeneratedClass::class),
            '?' . DummyGeneratedClass::class
        ];
    }

    /**
     * @test
     */
    public function generated_class_should_have_public_constructor_with_a_parameter_for_each_field(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['firstName' => Scalar::STRING(), 'lastName' => Scalar::STRING()]
        );

        $this->generateAndEvaluate($definition);

        self::assertClassHasPublicMethod('__construct', $className);
        self::assertMethodHasParameters('__construct', ['firstName', 'lastName'], $className);
    }

    /**
     * @test
     */
    public function object_can_be_constructed(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['firstName' => Scalar::STRING(), 'lastName' => Scalar::STRING()]
        );
        $this->generateAndEvaluate($definition);

        $result = new $className('Jane', 'Doe');

        self::assertInstanceOf($className, $result);
        self::assertSame('Jane', $result->firstName);
        self::assertSame('Doe', $result->lastName);
    }

    /**
     * @test
     */
    public function object_can_be_created_from_array(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['firstName' => Scalar::STRING(), 'lastName' => Scalar::STRING()]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['firstName' => 'Jane', 'lastName' => 'Doe']);

        self::assertInstanceOf($className, $result);
        self::assertSame('Jane', $result->firstName);
        self::assertSame('Doe', $result->lastName);
    }

    /**
     * @test
     */
    public function nullables_can_be_omitted(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['firstName' => new NonNullable(Scalar::STRING()), 'lastName' => Scalar::STRING()]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['firstName' => 'Jane']);

        self::assertInstanceOf($className, $result);
        self::assertSame('Jane', $result->firstName);
        self::assertNull($result->lastName);
    }

    /**
     * @test
     */
    public function from_array_list(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, ['names' => new ListType(Scalar::STRING())]);
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['names' => ['Alice', 'Bob', 'Carla']]);

        self::assertSame(['Alice', 'Bob', 'Carla'], $result->names);
    }

    /**
     * @test
     */
    public function nullable_list_without_data(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, ['names' => new ListType(Scalar::STRING())]);
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray([]);

        self::assertNull($result->names);
    }

    /**
     * @test
     */
    public function nullable_list_with_data(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, ['names' => new ListType(Scalar::STRING())]);
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['names' => ['Alice', 'Bob', 'Carla']]);

        self::assertSame(['Alice', 'Bob', 'Carla'], $result->names);
    }

    /**
     * @test
     */
    public function list_with_nullable_elements(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, ['names' => new ListType(Scalar::STRING())]);
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['names' => ['Alice', null, 'Carla']]);

        self::assertSame(['Alice', null, 'Carla'], $result->names);
    }

    /**
     * @test
     */
    public function generated_class(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['input' => new GeneratedClassType(DummyGeneratedClass::class)]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['input' => ['data']]);

        self::assertInstanceOf(DummyGeneratedClass::class, $result->input);
        self::assertSame(['data'], $result->input->data);
    }

    /**
     * @test
     */
    public function nullable_generated_class_without_value(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['input' => new GeneratedClassType(DummyGeneratedClass::class)]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray([]);

        self::assertNull($result->input);
    }

    /**
     * @test
     */
    public function nullable_generated_class_with_value(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['input' => new GeneratedClassType(DummyGeneratedClass::class)]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['input' => ['data']]);

        self::assertInstanceOf(DummyGeneratedClass::class, $result->input);
        self::assertSame(['data'], $result->input->data);
    }

    /**
     * @test
     */
    public function list_of_generated_class(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['input' => new NonNullable(new ListType(new GeneratedClassType(DummyGeneratedClass::class)))]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['input' => [[1], [2]]]);

        self::assertEquals(
            [new DummyGeneratedClass([1]), new DummyGeneratedClass([2])],
            $result->input
        );
    }

    /**
     * @test
     */
    public function list_of_nullable_generated_class(): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition(
            $className,
            ['input' => new ListType(new GeneratedClassType(DummyGeneratedClass::class))]
        );
        $this->generateAndEvaluate($definition);

        $result = $className::fromArray(['input' => [[1], null, [3]]]);

        self::assertEquals(
            [new DummyGeneratedClass([1]), null, new DummyGeneratedClass([3])],
            $result->input
        );
    }

    /**
     * @test
     *
     * @dataProvider invalidInput
     */
    public function exceptions(array $definition, array $input, string $expectedExceptionMessage): void
    {
        $className  = $this->randomClassName();
        $definition = new InputTypeDefinition($className, $definition);
        $this->generateAndEvaluate($definition);

        self::expectExceptionMessage($expectedExceptionMessage);

        $className::fromArray($input);
    }

    /**
     * @return Generator<string, array{0: array<string, Type>, 1: array<string, mixed>, 1: string}>
     */
    public function invalidInput(): Generator
    {
        $definition = [
            's' => new NonNullable(Scalar::STRING()),
            'i' => new NonNullable(Scalar::INTEGER()),
            'f' => new NonNullable(Scalar::FLOAT()),
            'b' => new NonNullable(Scalar::BOOLEAN())
        ];

        yield 'Required field is missing' => [
            $definition,
            ['s' => 'Jane'],
            'i must not be null'
        ];

        yield 'Invalid type' => [
            $definition,
            ['s' => 1, 'i' => 1, 'f' => 1.5, 'b' => true],
            's must be a string, got integer'
        ];

        yield 'generated class' => [
            ['names' => new GeneratedClassType(DummyGeneratedClass::class)],
            ['names' => 'foo'],
            'names must be a array, got string'
        ];

        yield 'List with non-nullable scalar type' => [
            ['names' => new ListType(new NonNullable(Scalar::STRING()))],
            ['names' => ['Alice', null, 'Carla']],
            'names[1] must not be null'
        ];

        yield 'List with non-nullable generated class' => [
            ['names' => new ListType(new NonNullable(new GeneratedClassType(DummyGeneratedClass::class)))],
            ['names' => [['Alice'], 'Carla']],
            'names[1] must be a array, got string'
        ];
    }
}
