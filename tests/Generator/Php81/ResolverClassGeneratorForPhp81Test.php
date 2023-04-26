<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php81;

use GraphQLGenerator\Generator\Php81\ResolverInterfaceGeneratorForPhp81;
use GraphQLGenerator\Generator\ResolverInterfaceGenerator;
use Tests\GraphQLGenerator\Generator\ResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php81\ResolverInterfaceGeneratorForPhp81
 */
final class ResolverClassGeneratorForPhp81Test extends ResolverClassGeneratorTest
{
    protected function subject(): ResolverInterfaceGenerator
    {
        return new ResolverInterfaceGeneratorForPhp81();
    }
}
