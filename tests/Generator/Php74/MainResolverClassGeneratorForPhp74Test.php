<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Generator\Php74;

use GraphQLGenerator\Generator\MainResolverClassGenerator;
use GraphQLGenerator\Generator\Php74\MainResolverClassGeneratorForPhp74;
use Tests\GraphQLGenerator\Generator\MainResolverClassGeneratorTest;

/**
 * @covers \GraphQLGenerator\Generator\Php74\MainResolverClassGeneratorForPhp74
 */
final class MainResolverClassGeneratorForPhp74Test extends MainResolverClassGeneratorTest
{
    private MainResolverClassGeneratorForPhp74 $subject;

    protected function subject(): MainResolverClassGenerator
    {
        return $this->subject;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new MainResolverClassGeneratorForPhp74();
    }
}
