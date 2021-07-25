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
use GraphQLGenerator\Config;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\Scalar;
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

    public function process(Config\Config $config): BuildDefinition
    {
        $classNamer = new DefaultClassNamer($config->target->namespacePrefix);
        $schema     = $this->buildSchema($config->schema);

        $inputTypes = [];
        $resolvers  = [];

        foreach ($config->types as $type => $class) {
            $this->types[$type] = new ExistingClassType($class);
        }

        foreach ($schema->getTypeMap() as $type) {
            if ($type instanceof InputObjectType) {
                $this->inputTypes[$type->name] = new GeneratedClassType($classNamer->inputType($type->name));
            }
        }

        foreach ($schema->getTypeMap() as $type) {
            if ($type instanceof InputObjectType) {
                $inputTypes[] = $this->inputTypeDefinition($type, $classNamer);
            }
        }

        foreach ($config->resolvers as $resolver) {
            $type = $schema->getType($resolver->type);
            if ($type === null) {
                throw new RuntimeException('Type does not exist: ' . $resolver->type);
            }

            if (!$type instanceof ObjectType) {
                throw new RuntimeException('Invalid type: ' . get_class($type));
            }

            if (!$type->hasField($resolver->field)) {
                throw new RuntimeException('Field missing: ' . $resolver->field);
            }

            $resolvers[] = $this->resolverDefinition($schema, $type, $type->getField($resolver->field), $classNamer);
        }

        $mainResolver = new MainResolverDefinition($classNamer->mainResolver(), $resolvers);

        return new BuildDefinition($inputTypes, $resolvers, $mainResolver);
    }

    private function buildSchema(Config\Schema $schema): Schema
    {
        $schemaContent = '';
        foreach ($schema->files as $schemaFile) {
            $schemaContent .= file_get_contents($schemaFile) . PHP_EOL;
        }

        return BuildSchema::build($schemaContent);
    }

    private function inputTypeDefinition(InputObjectType $type, ClassNamer $classNamer): InputTypeDefinition
    {
        $fields = [];
        foreach ($type->getFields() as $field) {
            $fields[$field->name] = $this->convertType($field->getType());
        }

        return new InputTypeDefinition($classNamer->inputType($type->name), $fields);
    }

    private function convertType(\GraphQL\Type\Definition\Type $type): Type
    {
        if ($type instanceof NonNull) {
            return new NonNullable($this->convertType($type->getWrappedType()));
        }

        if ($type instanceof ListOfType) {
            return new ListType($this->convertType($type->getWrappedType()));
        }

        switch (get_class($type)) {
            case StringType::class:
            case IDType::class:
            case EnumType::class:
            case CustomScalarType::class:
                return Scalar::STRING();
            case BooleanType::class:
                return Scalar::BOOLEAN();
            case IntType::class:
                return Scalar::INTEGER();
            case FloatType::class:
                return Scalar::FLOAT();
            case InputObjectType::class:
                return $this->inputTypeFor($type);
            case ObjectType::class:
                return $this->typeFor($type);
        }

        throw new RuntimeException(sprintf('Unhandled type: %s', get_class($type)));
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

    private function resolverDefinition(
        Schema $schema,
        \GraphQL\Type\Definition\Type $type,
        FieldDefinition $field,
        ClassNamer $classNamer
    ): ResolverDefinition {
        if ($schema->getQueryType() === $type || $schema->getMutationType() === $type) {
            $value = null;
        } else {
            $value = $this->typeFor($type);
        }

        $argumentClass = null;
        if (count($field->args) > 0) {
            $argumentClass = $this->argumentTypeDefinition($field, $classNamer);
        }

        return new ResolverDefinition(
            $classNamer->resolver($type->name, $field->name),
            $type->name,
            $field->name,
            $value,
            $argumentClass,
            $this->convertType($field->getType())
        );
    }

    private function argumentTypeDefinition(FieldDefinition $field, ClassNamer $classNamer): InputTypeDefinition
    {
        $fields = [];
        foreach ($field->args as $arg) {
            $fields[$arg->name] = $this->convertType($arg->getType());
        }

        return new InputTypeDefinition($classNamer->argumentType($field->name), $fields);
    }
}
