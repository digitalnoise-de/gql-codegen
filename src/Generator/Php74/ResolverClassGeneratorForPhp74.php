<?php
declare(strict_types=1);

namespace GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Generator\ResolverClassGenerator;
use GraphQLGenerator\ResolverDefinition;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;

final class ResolverClassGeneratorForPhp74 implements ResolverClassGenerator
{
    private const METHOD_NAME = '__invoke';

    public function generate(ResolverDefinition $resolver): GeneratedClass
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $class = $file->addClass($resolver->className);
        $class->setFinal(true);

        $method = $class->addMethod(self::METHOD_NAME);

        $this->addParametersAndReturnType($resolver, $method);

        return new GeneratedClass($resolver->className, (string)$file);
    }

    private function addParametersAndReturnType(ResolverDefinition $resolver, Method $method): void
    {
//        $method->setReturnType($resolver->returnType->phpType)
//            ->setReturnNullable($resolver->returnType->nullable);

        if ($resolver->valueType !== null) {
            $param = $method->addParameter('value');
            $param->setType($resolver->valueType->className);
        }

        if ($resolver->args !== null) {
            $param = $method->addParameter('args');
            $param->setType($resolver->args->className);
        }
    }

    public function update(ResolverDefinition $resolver, ClassType $class): ClassType
    {
        $result = clone $class;

        if ($result->hasMethod(self::METHOD_NAME)) {
            $method = $result->getMethod(self::METHOD_NAME);
            foreach ($method->getParameters() as $parameter) {
                $method->removeParameter($parameter->getName());
            }
        } else {
            $method = $result->addMethod(self::METHOD_NAME);
        }

        $this->addParametersAndReturnType($resolver, $method);

        return $result;
    }
}
