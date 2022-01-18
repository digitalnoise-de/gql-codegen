<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Build\MainResolverDefinition;
use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\MainResolverClassGenerator;
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

            $resolve->addBody(
                sprintf(
                    '    \'%s.%s\' => ($this->%s)(%s),',
                    $resolver->typeName,
                    $resolver->fieldName,
                    $name,
                    implode(', ', $args)
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
}
