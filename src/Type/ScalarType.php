<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @extends Enum<string>
 *
 * @method static self STRING()
 * @method static self BOOLEAN()
 * @method static self INTEGER()
 * @method static self FLOAT()
 */
final class ScalarType extends Enum implements Type
{
    private const STRING  = 'string';
    private const BOOLEAN = 'bool';
    private const INTEGER = 'int';
    private const FLOAT   = 'float';
}
