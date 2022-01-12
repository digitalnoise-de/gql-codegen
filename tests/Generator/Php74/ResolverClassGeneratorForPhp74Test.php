<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\Php74\ResolverInterfaceGeneratorForPhp74;
use GraphQLGenerator\Generator\ResolverInterfaceGenerator;
use Tests\GraphQLGenerator\Generator\ResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php74\ResolverInterfaceGeneratorForPhp74
 */
final class ResolverClassGeneratorForPhp74Test extends ResolverClassGeneratorTest
{
    protected function subject(): ResolverInterfaceGenerator
    {
        return new ResolverInterfaceGeneratorForPhp74();
    }
}
