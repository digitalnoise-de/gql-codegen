<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Config;

use GraphQLGenerator\Config\Config;
use GraphQLGenerator\Config\Resolver;
use GraphQLGenerator\Config\Schema;
use GraphQLGenerator\Config\Target;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GraphQLGenerator\Config\Config
 */
final class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_parse_a_configuration(): void
    {
        $root = vfsStream::setup();
        $file = new vfsStreamFile('config.xml');
        $file->setContent(
            '<?xml version="1.0"?>
<gqlCodegen xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="gql-codegen.xsd">
    <target namespacePrefix="App\GraphQL" directory="src/GraphQL" />
    <schema>
        <file name="queries.graphql" />
        <file name="mutations.graphql" />
    </schema>
    <types>
        <type name="Article">App\Model\Article</type>
        <type name="User">App\Model\User</type>
    </types>
    <resolvers>
        <resolver type="Query" field="users" />
        <resolver type="Query" field="articles" />
    </resolvers>
</gqlCodegen>'
        );
        $root->addChild($file);

        $config = Config::fromXmlFile($file->url());

        self::assertEquals(new Target('App\\GraphQL', 'src/GraphQL'), $config->target);
        self::assertEquals(new Schema(['queries.graphql', 'mutations.graphql']), $config->schema);
        self::assertEquals(
            [
                'Article' => 'App\\Model\\Article',
                'User'    => 'App\\Model\\User',
            ],
            $config->types
        );
        self::assertEquals(
            [
                new Resolver('Query', 'users'),
                new Resolver('Query', 'articles'),
            ],
            $config->resolvers
        );
    }
}
