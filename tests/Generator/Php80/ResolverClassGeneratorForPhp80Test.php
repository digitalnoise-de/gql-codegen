<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Generator\Php80\ResolverInterfaceGeneratorForPhp80;
use GraphQLGenerator\Generator\ResolverInterfaceGenerator;
use Tests\GraphQLGenerator\Generator\ResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php80\ResolverInterfaceGeneratorForPhp80
 */
final class ResolverClassGeneratorForPhp80Test extends ResolverClassGeneratorTest
{
    protected function subject(): ResolverInterfaceGenerator
    {
        return new ResolverInterfaceGeneratorForPhp80();
    }
}
