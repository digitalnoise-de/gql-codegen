<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\MainResolverDefinition;
use Nette\PhpGenerator\PhpFile;

final class MainResolverClassGeneratorForPhp74 implements MainResolverClassGenerator
{
    public function generate(MainResolverDefinition $definition): GeneratedClass
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $class = $file->addClass($definition->className);

        $ctor = $class->addMethod('__construct');
        foreach ($definition->resolvers as $resolver) {
            $class->addProperty($resolver->fieldName)->setType($resolver->className);
            $ctor->addParameter($resolver->fieldName)->setType($resolver->className);
            $ctor->addBody(sprintf('$this->%s = $%s;', $resolver->fieldName, $resolver->fieldName));
        }

        $resolve = $class->addMethod('resolve');
        $resolve->addComment('@param mixed $value');
        $resolve->addComment('@return mixed');
        $resolve->addParameter('type')
            ->setType('string');
        $resolve->addParameter('field')
            ->setType('string');
        $resolve->addParameter('value');
        $resolve->addParameter('args')
            ->setType('array');

        $resolve->addBody('$name = $type . \'.\' . $field;');
        $resolve->addBody('switch ($name) {');

        foreach ($definition->resolvers as $resolver) {
            $resolve->addBody(sprintf('    case \'%s.%s\':', $resolver->typeName, $resolver->fieldName));
            $resolve->addBody(sprintf('        return ($this->%s)(', $resolver->fieldName));
            if ($resolver->args !== null) {
                $resolve->addBody(sprintf('            \%s::fromArray($args)', $resolver->args->className));
            }
            $resolve->addBody('        );');
        }

        $resolve->addBody('}');

        return new GeneratedClass($definition->className, (string)$file);
    }
}
