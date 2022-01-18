<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator;

use function array_map;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Tests\GraphQLGenerator\Constraint\ClassHasPublicMethod;
use Tests\GraphQLGenerator\Constraint\ClassHasPublicProperty;

abstract class ClassGeneratorTestCase extends TestCase
{
    protected static function assertPropertyHasType(string $type, string $className, string $property): void
    {
        self::assertClassHasPublicProperty($property, $className);

        $rp = new ReflectionProperty($className, $property);

        self::assertSame($type, sprintf('%s%s', $rp->getType()->allowsNull() ? '?' : '', $rp->getType()->getName()));
    }

    protected static function assertClassHasPublicProperty(string $property, string $className): void
    {
        self::assertThat($className, new ClassHasPublicProperty($property));
    }

    /**
     * @psalm-return class-string
     */
    protected function randomClassName(): string
    {
        return sprintf('Generated\\Class%s', uniqid());
    }

    protected function assertClassHasPublicMethod(string $method, string $className): void
    {
        self::assertThat($className, new ClassHasPublicMethod($method));
    }

    /**
     * @param list<string> $methods
     * @param class-string $className
     */
    protected function assertClassHasPublicMethods(array $methods, string $className): void
    {
        $rc = new ReflectionClass($className);

        $publicMethods = array_map(
            static fn (ReflectionMethod $rm): string => $rm->name,
            $rc->getMethods(ReflectionMethod::IS_PUBLIC)
        );

        self::assertEqualsCanonicalizing($methods, $publicMethods);
    }

    protected function assertMethodHasParameters(string $method, array $parameters, string $className): void
    {
        $actualParameters = array_map(
            static fn (ReflectionParameter $rp) => $rp->getName(),
            (new ReflectionClass($className))->getMethod($method)->getParameters()
        );

        self::assertEquals($parameters, $actualParameters);
    }
}
