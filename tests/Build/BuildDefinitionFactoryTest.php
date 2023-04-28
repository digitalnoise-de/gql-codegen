<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Build;

use GraphQL\Utils\BuildSchema;
use GraphQLGenerator\Build\BuildDefinition;
use GraphQLGenerator\Build\BuildDefinitionFactory;
use GraphQLGenerator\Build\DefaultClassNamer;
use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Config\Resolver;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GraphQLGenerator\Build\BuildDefinitionFactory
 */
final class BuildDefinitionFactoryTest extends TestCase
{
    private BuildDefinitionFactory $subject;

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

        $definition = $this->build($schema, [], [new Resolver('Query', 'field')]);

        self::assertEquals(
            new ResolverDefinition(
                'T\\Resolver\\Query\\FieldResolver', 'Query', 'field', null, null, ScalarType::BOOLEAN()
            ),
            $definition->resolvers[0]
        );
    }

    /**
     * @test
     */
    public function resolver_with_arguments(): void
    {
        $schema = <<<end
type Query {
    field(name: String, age: Int): Boolean
}
end;

        $definition = $this->build($schema, [], [new Resolver('Query', 'field')]);

        self::assertEquals(
            new ResolverDefinition(
                'T\\Resolver\\Query\\FieldResolver',
                'Query',
                'field',
                null,
                new InputTypeDefinition(
                    'T\Resolver\Query\FieldArguments',
                    ['name' => ScalarType::STRING(), 'age' => ScalarType::INTEGER()]
                ),
                ScalarType::BOOLEAN()
            ),
            $definition->resolvers[0]
        );
    }

    /**
     * @test
     *
     * @dataProvider inputTypeExamples
     *
     * @testdox      Input field with type $type should be converted to $expectedType
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

        $definition = $this->build($schema, [], []);

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

        $definition = $this->build(
            $schema,
            ['ObjectType' => 'My\\ObjectType'],
            [new Resolver('ObjectType', 'field')]
        );

        self::assertEquals(
            new ResolverDefinition(
                'T\\Resolver\\ObjectType\\FieldResolver',
                'ObjectType',
                'field',
                new NonNullable(new ExistingClassType('My\\ObjectType')),
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

        $definition = $this->build($schema, [], []);

        self::assertEquals(
            [
                new InputTypeDefinition('T\\Type\\InputA', ['field1' => ScalarType::STRING()]),
                new InputTypeDefinition('T\\Type\\InputB', ['field1' => ScalarType::STRING()]),
            ],
            $definition->inputTypes
        );
    }

    /**
     * @param array<string, string> $types
     * @param list<Resolver>        $resolvers
     */
    private function build(string $schema, array $types, array $resolvers): BuildDefinition
    {
        return $this->subject->process(BuildSchema::build($schema), $types, $resolvers);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new BuildDefinitionFactory(new DefaultClassNamer('T'));
    }
}
