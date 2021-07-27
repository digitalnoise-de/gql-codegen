<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\ResolverClassGenerator;
use GraphQLGenerator\ResolverDefinition;
use Nette\PhpGenerator\PhpFile;

final class ResolverClassGeneratorForPhp74 implements ResolverClassGenerator
{
    private const METHOD_NAME = '__invoke';

    public function generate(ResolverDefinition $resolver): GeneratedClass
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $class = $file->addInterface($resolver->className);

        $method = $class->addMethod(self::METHOD_NAME);
        $method->setPublic();

        $returnType = TypeDetailsFactoryForPhp74::create($resolver->returnType);

        $method->setReturnType($returnType->phpType);
        $method->setReturnNullable($returnType->nullable);

        if ($returnType->docBlockType !== null) {
            $method->addComment(sprintf('@return %s', $returnType->docBlockType));
        }

        if ($resolver->valueType !== null) {
            $param = $method->addParameter('value');
            $param->setType($resolver->valueType->className);
        }

        if ($resolver->args !== null) {
            $param = $method->addParameter('args');
            $param->setType($resolver->args->className);
        }

        return new GeneratedClass($resolver->className, (string)$file);
    }
}
