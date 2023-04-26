<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php81;

use GraphQLGenerator\Generator\Php81\TypeDetailsFactoryForPhp81;
use GraphQLGenerator\Generator\TypeDetails;
use GraphQLGenerator\Type\ExistingClassType;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\ScalarType;
use GraphQLGenerator\Type\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GraphQLGenerator\Generator\Php81\TypeDetailsFactoryForPhp81
 */
final class TypeDetailsFactoryForPhp81Test extends TestCase
{
    /**
     * @test
     *
     * @dataProvider examples
     */
    public function type_details_generation(Type $type, TypeDetails $expected): void
    {
        self::assertEquals($expected, TypeDetailsFactoryForPhp81::create($type));
    }

    /**
     * @return \Generator<string, array{0: Type, 1: TypeDetails}>
     */
    public function examples(): \Generator
    {
        yield 'string' => [
            new NonNullable(ScalarType::STRING()),
            new TypeDetails('string', false, null),
        ];

        yield 'bool' => [
            new NonNullable(ScalarType::BOOLEAN()),
            new TypeDetails('bool', false, null),
        ];

        yield 'int' => [
            new NonNullable(ScalarType::INTEGER()),
            new TypeDetails('int', false, null),
        ];

        yield 'float' => [
            new NonNullable(ScalarType::FLOAT()),
            new TypeDetails('float', false, null),
        ];

        yield 'generated class' => [
            new NonNullable(new GeneratedClassType('My\Classname')),
            new TypeDetails('\My\Classname', false, null),
        ];

        yield 'existing class' => [
            new NonNullable(new ExistingClassType('My\Classname')),
            new TypeDetails('\My\Classname', false, null),
        ];

        yield 'nullable string' => [
            ScalarType::STRING(),
            new TypeDetails('string', true, null),
        ];

        yield 'nullable bool' => [
            ScalarType::BOOLEAN(),
            new TypeDetails('bool', true, null),
        ];

        yield 'nullable int' => [
            ScalarType::INTEGER(),
            new TypeDetails('int', true, null),
        ];

        yield 'nullable float' => [
            ScalarType::FLOAT(),
            new TypeDetails('float', true, null),
        ];

        yield 'nullable generated class' => [
            new GeneratedClassType('My\Classname'),
            new TypeDetails('\My\Classname', true, null),
        ];

        yield 'nullable existing class' => [
            new ExistingClassType('My\Classname'),
            new TypeDetails('\My\Classname', true, null),
        ];

        yield 'list of strings' => [
            new NonNullable(new ListType(new NonNullable(ScalarType::STRING()))),
            new TypeDetails('array', false, 'list<string>'),
        ];

        yield 'list of bool' => [
            new NonNullable(new ListType(new NonNullable(ScalarType::BOOLEAN()))),
            new TypeDetails('array', false, 'list<bool>'),
        ];

        yield 'list of int' => [
            new NonNullable(new ListType(new NonNullable(ScalarType::INTEGER()))),
            new TypeDetails('array', false, 'list<int>'),
        ];

        yield 'list of float' => [
            new NonNullable(new ListType(new NonNullable(ScalarType::FLOAT()))),
            new TypeDetails('array', false, 'list<float>'),
        ];

        yield 'list of generated class' => [
            new NonNullable(new ListType(new NonNullable(new GeneratedClassType('My\Classname')))),
            new TypeDetails('array', false, 'list<\My\Classname>'),
        ];

        yield 'list of existing class' => [
            new NonNullable(new ListType(new NonNullable(new ExistingClassType('My\Classname')))),
            new TypeDetails('array', false, 'list<\My\Classname>'),
        ];

        yield 'list of nullable strings' => [
            new NonNullable(new ListType(ScalarType::STRING())),
            new TypeDetails('array', false, 'list<string|null>'),
        ];

        yield 'list of nullable bool' => [
            new NonNullable(new ListType(ScalarType::BOOLEAN())),
            new TypeDetails('array', false, 'list<bool|null>'),
        ];

        yield 'list of nullable int' => [
            new NonNullable(new ListType(ScalarType::INTEGER())),
            new TypeDetails('array', false, 'list<int|null>'),
        ];

        yield 'list of nullable float' => [
            new NonNullable(new ListType(ScalarType::FLOAT())),
            new TypeDetails('array', false, 'list<float|null>'),
        ];

        yield 'list of nullable generated class' => [
            new NonNullable(new ListType(new GeneratedClassType('My\Classname'))),
            new TypeDetails('array', false, 'list<\My\Classname|null>'),
        ];

        yield 'list of nullable existing class' => [
            new NonNullable(new ListType(new ExistingClassType('My\Classname'))),
            new TypeDetails('array', false, 'list<\My\Classname|null>'),
        ];

        yield 'nullable list of strings' => [
            new ListType(new NonNullable(ScalarType::STRING())),
            new TypeDetails('array', true, 'list<string>|null'),
        ];

        yield 'nullable list of bool' => [
            new ListType(new NonNullable(ScalarType::BOOLEAN())),
            new TypeDetails('array', true, 'list<bool>|null'),
        ];

        yield 'nullable list of int' => [
            new ListType(new NonNullable(ScalarType::INTEGER())),
            new TypeDetails('array', true, 'list<int>|null'),
        ];

        yield 'nullable list of float' => [
            new ListType(new NonNullable(ScalarType::FLOAT())),
            new TypeDetails('array', true, 'list<float>|null'),
        ];

        yield 'nullable list of generated class' => [
            new ListType(new NonNullable(new GeneratedClassType('My\Classname'))),
            new TypeDetails('array', true, 'list<\My\Classname>|null'),
        ];

        yield 'nullable list of existing class' => [
            new ListType(new NonNullable(new ExistingClassType('My\Classname'))),
            new TypeDetails('array', true, 'list<\My\Classname>|null'),
        ];

        yield 'nullable list of nullable strings' => [
            new ListType(ScalarType::STRING()),
            new TypeDetails('array', true, 'list<string|null>|null'),
        ];

        yield 'nullable list of nullable bool' => [
            new ListType(ScalarType::BOOLEAN()),
            new TypeDetails('array', true, 'list<bool|null>|null'),
        ];

        yield 'nullable list of nullable int' => [
            new ListType(ScalarType::INTEGER()),
            new TypeDetails('array', true, 'list<int|null>|null'),
        ];

        yield 'nullable list of nullable float' => [
            new ListType(ScalarType::FLOAT()),
            new TypeDetails('array', true, 'list<float|null>|null'),
        ];

        yield 'nullable list of nullable generated class' => [
            new ListType(new GeneratedClassType('My\Classname')),
            new TypeDetails('array', true, 'list<\My\Classname|null>|null'),
        ];

        yield 'nullable list of nullable existing class' => [
            new ListType(new ExistingClassType('My\Classname')),
            new TypeDetails('array', true, 'list<\My\Classname|null>|null'),
        ];
    }
}
