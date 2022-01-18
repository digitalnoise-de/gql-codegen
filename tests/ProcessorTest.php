<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator;

use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Config\Resolver;
use GraphQLGenerator\DefaultClassNamer;
use GraphQLGenerator\Processor;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GraphQLGenerator\Processor
 */
final class ProcessorTest extends TestCase
{
    private Processor $subject;

    /**
     * @test
     */
    public function resolver_without_value_type_and_argument(): void
    {
        $schema = <<<end
type Query {
    field: Boolean
}
end;

        $definition = $this->subject->process($schema, [], [new Resolver('Query', 'field')]);

        self::assertEquals(
            new ResolverDefinition(
                'T\\Resolver\\Query\\FieldResolver', 'Query', 'field', null, null, ScalarType::BOOLEAN()
            ),
            $definition->resolvers[0]
        );
    }

    /**
     * @test
     *
     * @dataProvider inputTypeExamples
     *
     * @testdox Input field with type $type should be converted to $expectedType
     */
    public function input_type(string $type, Type $expectedType): void
    {
        $schema = <<<end
input TestInput {
    value: $type
}

input OtherInput {
    value: String
}
end;

        $definition = $this->subject->process($schema, [], []);

        self::assertEquals($expectedType, $definition->inputTypes[0]->fields['value'] ?? null);
    }

    public function inputTypeExamples(): iterable
    {
        yield ['String', ScalarType::STRING()];
        yield ['String!', new NonNullable(ScalarType::STRING())];
        yield ['[String]', new ListType(ScalarType::STRING())];
        yield ['[String!]', new ListType(new NonNullable(ScalarType::STRING()))];
        yield ['[String!]!', new NonNullable(new ListType(new NonNullable(ScalarType::STRING())))];

        yield ['OtherInput', new GeneratedClassType('T\Type\OtherInput')];
        yield ['OtherInput!', new NonNullable(new GeneratedClassType('T\Type\OtherInput'))];
    }

    /**
     * @test
     */
    public function resolver_with_value_type_but_without_argument(): void
    {
        $schema = <<<end
type ObjectType {
    field: Boolean
}
end;

        $definition = $this->subject->process(
            $schema,
            ['ObjectType' => 'My\\ObjectType'],
            [new Resolver('ObjectType', 'field')]
        );

        self::assertEquals(
            new ResolverDefinition(
                'T\\Resolver\\ObjectType\\FieldResolver',
                'ObjectType',
                'field',
                new ExistingClassType('My\\ObjectType'),
                null,
                ScalarType::BOOLEAN()
            ),
            $definition->resolvers[0]
        );
    }

    /**
     * @test
     */
    public function it_should_have_input_type_definitions(): void
    {
        $schema = <<<end
type TypeA {
    name: String
}

input InputA {
    field1: String
}

input InputB {
    field1: String
}
end;

        $definition = $this->subject->process($schema, [], []);

        self::assertEquals(
            [
                new InputTypeDefinition('T\\Type\\InputA', ['field1' => ScalarType::STRING()]),
                new InputTypeDefinition('T\\Type\\InputB', ['field1' => ScalarType::STRING()]),
            ],
            $definition->inputTypes
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Processor(new DefaultClassNamer('T'));
    }
}
