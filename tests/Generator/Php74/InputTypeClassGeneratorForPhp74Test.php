<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\InputTypeClassGenerator;
use GraphQLGenerator\Generator\Php74\InputTypeClassGeneratorForPhp74;
use Tests\GraphQLGenerator\Generator\InputTypeClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php74\InputTypeClassGeneratorForPhp74
 */
final class InputTypeClassGeneratorForPhp74Test extends InputTypeClassGeneratorTest
{
    protected function subject(): InputTypeClassGenerator
    {
        return new InputTypeClassGeneratorForPhp74();
    }
}
