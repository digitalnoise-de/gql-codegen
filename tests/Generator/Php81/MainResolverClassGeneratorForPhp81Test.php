<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php81;

use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\Generator\Php81\MainResolverClassGeneratorForPhp81;
use Tests\GraphQLGenerator\Generator\MainResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php81\MainResolverClassGeneratorForPhp81
 */
final class MainResolverClassGeneratorForPhp81Test extends MainResolverClassGeneratorTest
{
    private MainResolverClassGeneratorForPhp81 $subject;

    protected function subject(): MainResolverClassGenerator
    {
        return $this->subject;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new MainResolverClassGeneratorForPhp81();
    }
}
