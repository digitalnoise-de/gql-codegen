<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php81;

use GraphQLGenerator\Generator\InputTypeClassGenerator;
use GraphQLGenerator\Generator\Php81\InputTypeClassGeneratorForPhp81;
use Tests\GraphQLGenerator\Generator\InputTypeClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php81\InputTypeClassGeneratorForPhp81
 */
final class InputTypeClassGeneratorForPhp81Test extends InputTypeClassGeneratorTest
{
    protected function subject(): InputTypeClassGenerator
    {
        return new InputTypeClassGeneratorForPhp81();
    }
}
