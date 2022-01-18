<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php80;

use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\Generator\Php80\MainResolverClassGeneratorForPhp80;
use Tests\GraphQLGenerator\Generator\MainResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php80\MainResolverClassGeneratorForPhp80
 */
final class MainResolverClassGeneratorForPhp80Test extends MainResolverClassGeneratorTest
{
    private MainResolverClassGeneratorForPhp80 $subject;

    protected function subject(): MainResolverClassGenerator
    {
        return $this->subject;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new MainResolverClassGeneratorForPhp80();
    }
}
