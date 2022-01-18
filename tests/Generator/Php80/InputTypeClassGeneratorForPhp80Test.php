<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Generator\InputTypeClassGenerator;
use GraphQLGenerator\Generator\Php80\InputTypeClassGeneratorForPhp80;
use Tests\GraphQLGenerator\Generator\InputTypeClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php80\InputTypeClassGeneratorForPhp80
 */
final class InputTypeClassGeneratorForPhp80Test extends InputTypeClassGeneratorTest
{
    protected function subject(): InputTypeClassGenerator
    {
        return new InputTypeClassGeneratorForPhp80();
    }
}
