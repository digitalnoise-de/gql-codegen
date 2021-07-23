<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\Php74\ResolverClassGeneratorForPhp74;
use GraphQLGenerator\Generator\ResolverClassGenerator;
use Tests\GraphQLGenerator\Generator\ResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php74\ResolverClassGeneratorForPhp74
 */
final class ResolverClassGeneratorForPhp74Test extends ResolverClassGeneratorTest
{
    protected function subject(): ResolverClassGenerator
    {
        return new ResolverClassGeneratorForPhp74();
    }
}
