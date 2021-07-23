<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php74;

use Generator;
use GraphQLGenerator\Generator\Php74\TypeDetailsFactoryForPhp74;
use GraphQLGenerator\Generator\TypeDetails;
use GraphQLGenerator\Type\GeneratedClassType;
use GraphQLGenerator\Type\ListType;
use GraphQLGenerator\Type\NonNullable;
use GraphQLGenerator\Type\Scalar;
use GraphQLGenerator\Type\Type;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GraphQLGenerator\Generator\Php74\TypeDetailsFactoryForPhp74
 */
final class TypeDetailsFactoryForPhp74Test extends TestCase
{
    /**
     * @test
     *
     * @dataProvider examples
     */
    public function type_details_generation(Type $type, TypeDetails $expected): void
    {
        self::assertEquals($expected, TypeDetailsFactoryForPhp74::create($type));
    }

    /**
     * @return Generator<string, array{0: Type, 1: TypeDetails}>
     */
    public function examples(): Generator
    {
        yield 'string' => [
            new NonNullable(Scalar::STRING()),
            new TypeDetails('string', false, null)
        ];
        yield 'bool' => [
            new NonNullable(Scalar::BOOLEAN()),
            new TypeDetails('bool', false, null)
        ];
        yield 'int' => [
            new NonNullable(Scalar::INTEGER()),
            new TypeDetails('int', false, null)
        ];
        yield 'float' => [
            new NonNullable(Scalar::FLOAT()),
            new TypeDetails('float', false, null)
        ];
        yield 'generated class' => [
            new NonNullable(new GeneratedClassType('My\Classname')),
            new TypeDetails('\My\Classname', false, null)
        ];


        yield 'nullable string' => [
            Scalar::STRING(),
            new TypeDetails('string', true, null)
        ];

        yield 'nullable bool' => [
            Scalar::BOOLEAN(),
            new TypeDetails('bool', true, null)
        ];

        yield 'nullable int' => [
            Scalar::INTEGER(),
            new TypeDetails('int', true, null)
        ];

        yield 'nullable float' => [
            Scalar::FLOAT(),
            new TypeDetails('float', true, null)
        ];

        yield 'nullable generated class' => [
            new GeneratedClassType('My\Classname'),
            new TypeDetails('\My\Classname', true, null)
        ];


        yield 'list of strings' => [
            new NonNullable(new ListType(new NonNullable(Scalar::STRING()))),
            new TypeDetails('array', false, 'list<string>')
        ];

        yield 'list of bool' => [
            new NonNullable(new ListType(new NonNullable(Scalar::BOOLEAN()))),
            new TypeDetails('array', false, 'list<bool>')
        ];

        yield 'list of int' => [
            new NonNullable(new ListType(new NonNullable(Scalar::INTEGER()))),
            new TypeDetails('array', false, 'list<int>')
        ];

        yield 'list of float' => [
            new NonNullable(new ListType(new NonNullable(Scalar::FLOAT()))),
            new TypeDetails('array', false, 'list<float>')
        ];

        yield 'list of generated class' => [
            new NonNullable(new ListType(new NonNullable(new GeneratedClassType('My\Classname')))),
            new TypeDetails('array', false, 'list<\My\Classname>')
        ];


        yield 'list of nullable strings' => [
            new NonNullable(new ListType(Scalar::STRING())),
            new TypeDetails('array', false, 'list<string|null>')
        ];

        yield 'list of nullable bool' => [
            new NonNullable(new ListType(Scalar::BOOLEAN())),
            new TypeDetails('array', false, 'list<bool|null>')
        ];

        yield 'list of nullable int' => [
            new NonNullable(new ListType(Scalar::INTEGER())),
            new TypeDetails('array', false, 'list<int|null>')
        ];

        yield 'list of nullable float' => [
            new NonNullable(new ListType(Scalar::FLOAT())),
            new TypeDetails('array', false, 'list<float|null>')
        ];

        yield 'list of nullable generated class' => [
            new NonNullable(new ListType(new GeneratedClassType('My\Classname'))),
            new TypeDetails('array', false, 'list<\My\Classname|null>')
        ];


        yield 'nullable list of strings' => [
            new ListType(new NonNullable(Scalar::STRING())),
            new TypeDetails('array', true, 'list<string>|null')
        ];

        yield 'nullable list of bool' => [
            new ListType(new NonNullable(Scalar::BOOLEAN())),
            new TypeDetails('array', true, 'list<bool>|null')
        ];

        yield 'nullable list of int' => [
            new ListType(new NonNullable(Scalar::INTEGER())),
            new TypeDetails('array', true, 'list<int>|null')
        ];

        yield 'nullable list of float' => [
            new ListType(new NonNullable(Scalar::FLOAT())),
            new TypeDetails('array', true, 'list<float>|null')
        ];

        yield 'nullable list of generated class' => [
            new ListType(new NonNullable(new GeneratedClassType('My\Classname'))),
            new TypeDetails('array', true, 'list<\My\Classname>|null')
        ];


        yield 'nullable list of nullable strings' => [
            new ListType(Scalar::STRING()),
            new TypeDetails('array', true, 'list<string|null>|null')
        ];

        yield 'nullable list of nullable bool' => [
            new ListType(Scalar::BOOLEAN()),
            new TypeDetails('array', true, 'list<bool|null>|null')
        ];

        yield 'nullable list of nullable int' => [
            new ListType(Scalar::INTEGER()),
            new TypeDetails('array', true, 'list<int|null>|null')
        ];

        yield 'nullable list of nullable float' => [
            new ListType(Scalar::FLOAT()),
            new TypeDetails('array', true, 'list<float|null>|null')
        ];

        yield 'nullable list of nullable generated class' => [
            new ListType(new GeneratedClassType('My\Classname')),
            new TypeDetails('array', true, 'list<\My\Classname|null>|null')
        ];
    }
}
