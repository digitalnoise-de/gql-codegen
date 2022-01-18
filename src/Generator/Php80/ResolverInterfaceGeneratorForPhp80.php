<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Build\ResolverDefinition;
use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\ResolverInterfaceGenerator;
use Nette\PhpGenerator\PhpFile;

final class ResolverInterfaceGeneratorForPhp80 implements ResolverInterfaceGenerator
{
    private const METHOD_NAME = '__invoke';

    public function generate(ResolverDefinition $resolver): GeneratedClass
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $class = $file->addInterface($resolver->className);

        $method = $class->addMethod(self::METHOD_NAME);
        $method->setPublic();

        $returnType = TypeDetailsFactoryForPhp80::create($resolver->returnType);

        $method->setReturnType($returnType->phpType);
        $method->setReturnNullable($returnType->nullable);

        if ($returnType->docBlockType !== null) {
            $method->addComment(sprintf('@return %s', $returnType->docBlockType));
        }

        if ($resolver->valueType !== null) {
            $valueType = TypeDetailsFactoryForPhp80::create($resolver->valueType);

            $param = $method->addParameter('value');
            $param->setType($valueType->phpType);
            $param->setNullable($valueType->nullable);
        }

        if ($resolver->args !== null) {
            $param = $method->addParameter('args');
            $param->setType($resolver->args->className);
        }

        return new GeneratedClass($resolver->className, (string)$file);
    }
}
