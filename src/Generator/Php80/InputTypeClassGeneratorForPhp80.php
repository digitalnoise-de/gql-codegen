<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Build\InputTypeDefinition;
use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\InputTypeClassGenerator;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;
use LogicException;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;

final class InputTypeClassGeneratorForPhp80 implements InputTypeClassGenerator
{
    public function generate(InputTypeDefinition $definition): GeneratedClass
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $class = $file->addClass($definition->className);

        $class->addMember($this->constructorMethod($definition->fields));
        $class->addMember($this->factoryMethod($definition->fields));

        foreach ($definition->fields as $name => $type) {
            $class->addMember($this->property($name, $type));
            $class->addMember($this->sanitizeMethod($name, $type));

            if ($type instanceof ListType) {
                $class->addMember($this->sanitizeListElementMethod($name, $type->elementType));
            }

            if ($type instanceof NonNullable && $type->elementType instanceof ListType) {
                $class->addMember($this->sanitizeListElementMethod($name, $type->elementType->elementType));
            }
        }

        return new GeneratedClass($definition->className, (string)$file);
    }

    /**
     * @param array<string, Type> $fields
     */
    private function constructorMethod(array $fields): Method
    {
        $method = new Method('__construct');
        $method->setVisibility('public');

        foreach ($fields as $name => $type) {
            $typeDetails = TypeDetailsFactoryForPhp80::create($type);

            $param = $method->addParameter($name);
            $param->setType($typeDetails->phpType);
            $param->setNullable($typeDetails->nullable);

            if ($typeDetails->docBlockType !== null) {
                $method->addComment(sprintf('@param %s $%s', $typeDetails->docBlockType, $name));
            }

            $method->addBody(sprintf('$this->%s = $%s;', $name, $name));
        }

        return $method;
    }

    /**
     * @param array<string, Type> $fields
     */
    private function factoryMethod(array $fields): Method
    {
        $method = new Method('fromArray');
        $method->setStatic(true);
        $method->addParameter('array')->setType('array');
        $method->setReturnType('self');
        $method->setVisibility('public');
        $method->addComment('@throws \RuntimeException');

        $factoryArgs = [];
        foreach (array_keys($fields) as $name) {
            $factoryArgs[] = sprintf('self::%s($array[\'%s\'] ?? null)', $this->sanitizeMethodName($name), $name);
        }

        $method->addBody(sprintf('return new self(%s);', implode(', ', $factoryArgs)));

        return $method;
    }

    private function sanitizeMethodName(string $name): string
    {
        return sprintf('sanitize%s', ucfirst($name));
    }

    private function property(string $name, Type $type): Property
    {
        $typeDetails = TypeDetailsFactoryForPhp80::create($type);

        $property = new Property($name);
        $property->setPublic();
        $property->setType($typeDetails->phpType);
        $property->setNullable($typeDetails->nullable);

        if ($typeDetails->docBlockType !== null) {
            $property->addComment(sprintf('@var %s', $typeDetails->docBlockType));
        }

        return $property;
    }

    private function sanitizeMethod(string $name, Type $type): Method
    {
        $typeDetails = TypeDetailsFactoryForPhp80::create($type);

        $method = new Method($this->sanitizeMethodName($name));
        $method->setStatic(true);
        $method->setPrivate();
        $method->addParameter('value')->setType('mixed');
        $method->setReturnType($typeDetails->phpType);
        $method->setReturnNullable($typeDetails->nullable);

        if ($typeDetails->docBlockType !== null) {
            $method->addComment(sprintf('@return %s', $typeDetails->docBlockType));
        }

        $this->addSanitizeMethodBody($method, $type, $name);

        return $method;
    }

    private function addSanitizeMethodBody(Method $method, Type $type, string $name): void
    {
        $method->addComment('@throws \RuntimeException');

        $fieldName = count($method->getParameters()) === 1
            ? $name
            : sprintf('%s[\' . $index . \']', $name);

        $method->addBody('if ($value === null) {');
        if ($type instanceof NonNullable) {
            $method->addBody(sprintf('    throw new \RuntimeException(\'%s must not be null\');', $fieldName));
            $type = $type->elementType;
        } else {
            $method->addBody('    return null;');
        }
        $method->addBody('}');

        $method->addBody(sprintf('if (%s) {', $this->typeCheckCondition('$value', $type)));
        $method->addBody(
            sprintf(
                '    throw new \RuntimeException(\'%s must be a %s, got \' . gettype($value));',
                $fieldName,
                $type instanceof ScalarType ? $type->getValue() : 'array',
            )
        );
        $method->addBody('}');

        if ($type instanceof ListType) {
            $method->addBody(
                'return array_map([self::class, ?], $value, array_keys($value));',
                [$this->sanitizeListElementMethodName($name)]
            );

            return;
        }

        if ($type instanceof GeneratedClassType) {
            $method->addBody(sprintf('return \%s::fromArray($value);', $type->className));

            return;
        }

        $method->addBody('return $value;');
    }

    private function typeCheckCondition(string $variable, Type $type): string
    {
        if ($type instanceof ListType) {
            return sprintf('!is_array(%s)', $variable);
        }

        if ($type instanceof NonNullable) {
            return sprintf('%s !== null && %s', $variable, $this->typeCheckCondition($variable, $type->elementType));
        }

        if ($type instanceof GeneratedClassType) {
            return sprintf('!is_array(%s)', $variable);
        }

        if ($type instanceof ScalarType) {
            if ($type->equals(ScalarType::STRING())) {
                return sprintf('!is_string(%s)', $variable);
            }

            if ($type->equals(ScalarType::BOOLEAN())) {
                return sprintf('!is_bool(%s)', $variable);
            }

            if ($type->equals(ScalarType::INTEGER())) {
                return sprintf('!is_int(%s)', $variable);
            }

            if ($type->equals(ScalarType::FLOAT())) {
                return sprintf('!is_float(%s)', $variable);
            }
        }

        throw new LogicException(sprintf('Unsupported type of class %s', get_class($type)));
    }

    private function sanitizeListElementMethodName(string $name): string
    {
        return sprintf('sanitize%sListElement', ucfirst($name));
    }

    private function sanitizeListElementMethod(string $name, Type $type): Method
    {
        $typeDetails = TypeDetailsFactoryForPhp80::create($type);

        $method = new Method($this->sanitizeListElementMethodName($name));
        $method->setStatic(true);
        $method->setPrivate();
        $method->addParameter('value')->setType('mixed');
        $method->addParameter('index')->setType('string|int');
        $method->setReturnType($typeDetails->phpType);
        $method->setReturnNullable($typeDetails->nullable);

        if ($typeDetails->docBlockType !== null) {
            $method->addComment(sprintf('@return %s', $typeDetails->docBlockType));
        }

        $this->addSanitizeMethodBody($method, $type, $name);

        return $method;
    }
}
