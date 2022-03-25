<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;
use LogicException;
use Nette\PhpGenerator\PhpFile;

final class MainResolverClassGeneratorForPhp80 implements MainResolverClassGenerator
{
    public function generate(MainResolverDefinition $definition): GeneratedClass
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $class = $file->addClass($definition->className);

        $ctor = $class->addMethod('__construct');
        foreach ($definition->resolvers as $resolver) {
            $name = lcfirst($resolver->typeName) . ucfirst($resolver->fieldName);

            $class->addProperty($name)->setType($resolver->className);
            $ctor->addParameter($name)->setType($resolver->className);
            $ctor->addBody(sprintf('$this->%s = $%s;', $name, $name));
        }

        $resolve = $class->addMethod('resolve');
        $resolve->setReturnType('mixed');
        $resolve->addParameter('type')
            ->setType('string');
        $resolve->addParameter('field')
            ->setType('string');
        $resolve->addParameter('value')
            ->setType('mixed');
        $resolve->addParameter('args')
            ->setType('array');

        $resolve->addBody('return match ($type . \'.\' . $field) {');

        foreach ($definition->resolvers as $resolver) {
            $name = lcfirst($resolver->typeName) . ucfirst($resolver->fieldName);

            $args = [];
            if ($resolver->valueType !== null) {
                $args[] = '$value';
            }

            if ($resolver->args !== null) {
                $args[] = sprintf('\%s::fromArray($args)', $resolver->args->className);
            }

            $resolveMethodName = sprintf('resolve%s', ucfirst($name));

            $method = $class->addMethod($resolveMethodName);
            $method->addParameter('value')
                ->setType('mixed');

            $method->addParameter('args')
                ->setType('array');


            if ($resolver->valueType !== null) {
                $method->addBody(sprintf('    if (%s) {', $this->typeCheckFor($resolver->valueType, '$value')));
                $method->addBody(
                    sprintf(
                        '        throw new \RuntimeException(\'%s.%s expectes value of type "%s", got "\' . gettype($value) . \'"\');',
                        $resolver->typeName,
                        $resolver->fieldName,
                        $this->typeName($resolver->valueType)
                    )
                );
                $method->addBody('    }');
            }

            $method->addBody(sprintf('    return ($this->%s)(%s);', $name, implode(', ', $args)));

            $resolve->addBody(
                sprintf(
                    '    \'%s.%s\' => $this->%s($value, $args),',
                    $resolver->typeName,
                    $resolver->fieldName,
                    $resolveMethodName,
                )
            );
        }

        $resolve->addBody('};');

        $canResolve = $class->addMethod('canResolve');
        $canResolve->setReturnType('bool');
        $canResolve->addParameter('type')
            ->setType('string');
        $canResolve->addParameter('field')
            ->setType('string');

        $resolverNames = [];
        foreach ($definition->resolvers as $resolver) {
            $resolverNames[] = sprintf('%s.%s', $resolver->typeName, $resolver->fieldName);
        }

        $canResolve->addBody('return in_array($type . \'.\' . $field, ?);', [$resolverNames]);

        return new GeneratedClass($definition->className, (string)$file);
    }

    private function typeCheckFor(Type $type, string $variable): string
    {
        if ($type instanceof NonNullable) {
            return self::typeCheckFor($type->elementType, $variable);
        }

        if ($type instanceof ExistingClassType) {
            return sprintf('!%s instanceof \\%s', $variable, $type->className);
        }

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

        throw new LogicException();
    }

    private function typeName(Type $type): string
    {
        if ($type instanceof NonNullable) {
            return $this->typeName($type->elementType);
        }

        if ($type instanceof ExistingClassType) {
            return $type->className;
        }

        if ($type instanceof ScalarType) {
            return $type->getValue();
        }

        throw new LogicException();
    }
}
