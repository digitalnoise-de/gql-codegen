<?php
declare(strict_types=1);

namespace GraphQLGenerator;

use GraphQL\Type\Definition\BooleanType;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\FloatType;
use GraphQL\Type\Definition\IDType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\StringType;
use GraphQL\Type\Schema;
use GraphQL\Utils\BuildSchema;
use GraphQLGenerator\Build\BuildDefinition;
use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Config\Resolver;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;
use RuntimeException;

final class Processor
{
    /**
     * @var array<string, GeneratedClassType>
     */
    private array $inputTypes = [];

    /**
     * @var array<string, ExistingClassType>
     */
    private array $types = [];

    public function __construct(private readonly ClassNamer $classNamer)
    {
    }

    /**
     * @param array<string, string> $types
     * @param list<Resolver> $resolvers
     */
    public function process(string $schemaContent, array $types, array $resolvers): BuildDefinition
    {
        $schema = BuildSchema::build($schemaContent);

        foreach ($types as $type => $class) {
            $this->types[$type] = new ExistingClassType($class);
        }

        foreach ($schema->getTypeMap() as $type) {
            if ($type instanceof InputObjectType) {
                $this->inputTypes[$type->name] = new GeneratedClassType($this->classNamer->inputType($type->name));
            }
        }

        $inputDefs       = $this->inputDefinitions($schema);
        $resolverDefs    = $this->resolverDefinitions($schema, $resolvers);
        $mainResolverDef = new MainResolverDefinition($this->classNamer->mainResolver(), $resolverDefs);

        return new BuildDefinition($inputDefs, $resolverDefs, $mainResolverDef);
    }

    /**
     * @return list<InputTypeDefinition>
     */
    private function inputDefinitions(Schema $schema): array
    {
        $result = [];

        foreach ($schema->getTypeMap() as $type) {
            if ($type instanceof InputObjectType) {
                $result[] = $this->inputTypeDefinition($type);
            }
        }

        return $result;
    }

    private function inputTypeDefinition(InputObjectType $type): InputTypeDefinition
    {
        $fields = [];
        foreach ($type->getFields() as $field) {
            $fields[$field->name] = $this->convertType($field->getType());
        }

        return new InputTypeDefinition($this->classNamer->inputType($type->name), $fields);
    }

    private function convertType(\GraphQL\Type\Definition\Type $type): Type
    {
        if ($type instanceof NonNull) {
            /** @psalm-suppress ArgumentTypeCoercion */
            return new NonNullable($this->convertType($type->getWrappedType()));
        }

        if ($type instanceof ListOfType) {
            /** @psalm-suppress ArgumentTypeCoercion */
            return new ListType($this->convertType($type->getWrappedType()));
        }

        switch ($type::class) {
            case StringType::class:
            case IDType::class:
            case EnumType::class:
            case CustomScalarType::class:
                return ScalarType::STRING();
            case BooleanType::class:
                return ScalarType::BOOLEAN();
            case IntType::class:
                return ScalarType::INTEGER();
            case FloatType::class:
                return ScalarType::FLOAT();
            case InputObjectType::class:
                return $this->inputTypeFor($type);
            case ObjectType::class:
                return $this->typeFor($type);
        }

        throw new RuntimeException(sprintf('Unhandled type: %s', $type::class));
    }

    private function inputTypeFor(\GraphQL\Type\Definition\Type $type): GeneratedClassType
    {
        if (!isset($this->inputTypes[$type->name])) {
            throw new RuntimeException(sprintf('No input type for %s', $type->name));
        }

        return $this->inputTypes[$type->name];
    }

    private function typeFor(\GraphQL\Type\Definition\Type $type): ExistingClassType
    {
        if (!isset($this->types[$type->name])) {
            throw new RuntimeException(sprintf('No type for %s', $type->name));
        }

        return $this->types[$type->name];
    }

    /**
     * @param list<Resolver> $resolvers
     *
     * @return list<ResolverDefinition>
     */
    private function resolverDefinitions(Schema $schema, array $resolvers): array
    {
        $result = [];

        foreach ($resolvers as $resolver) {
            $type = $schema->getType($resolver->type);
            if ($type === null) {
                throw new RuntimeException('Type does not exist: '.$resolver->type);
            }

            if (!$type instanceof ObjectType) {
                throw new RuntimeException('Invalid type: '.$type::class);
            }

            if (!$type->hasField($resolver->field)) {
                throw new RuntimeException(
                    sprintf('Field "%s" does not exist on type "%s"', $resolver->field, $type->name)
                );
            }

            $result[] = $this->resolverDefinition($schema, $type, $type->getField($resolver->field));
        }

        return $result;
    }

    private function resolverDefinition(
        Schema $schema,
        \GraphQL\Type\Definition\Type $type,
        FieldDefinition $field
    ): ResolverDefinition {
        if ($schema->getQueryType() === $type || $schema->getMutationType() === $type) {
            $value = null;
        } else {
            $value = new NonNullable($this->typeFor($type));
        }

        $argumentClass = null;
        if (count($field->args) > 0) {
            $argumentClass = $this->argumentTypeDefinition($type, $field);
        }

        return new ResolverDefinition(
            $this->classNamer->resolver($type->name, $field->name),
            $type->name,
            $field->name,
            $value,
            $argumentClass,
            $this->convertType($field->getType())
        );
    }

    private function argumentTypeDefinition(
        \GraphQL\Type\Definition\Type $type,
        FieldDefinition $field
    ): InputTypeDefinition {
        $fields = [];
        foreach ($field->args as $arg) {
            $fields[$arg->name] = $this->convertType($arg->getType());
        }

        return new InputTypeDefinition($this->classNamer->argumentType($type->name, $field->name), $fields);
    }
}
