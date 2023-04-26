<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Config;

use GraphQLGenerator\Config\Config;
use GraphQLGenerator\Config\Endpoint;
use GraphQLGenerator\Config\Resolver;
use GraphQLGenerator\Config\Schema;
use GraphQLGenerator\Config\Target;
use org\bovigo\vfs\vfsStream;
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
        $root = vfsStream::setup('root', null, [
            'config.xml' => '<?xml version="1.0"?>
<gqlCodegen xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="gql-codegen.xsd">
    <endpoint>
        <target namespacePrefix="GraphQL\EndpointOne" directory="src/GraphQL/EndpointOne" />
        <schema>
            <directory name="vfs://root/src/SchemaDirectory" />
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
    </endpoint>
    <endpoint>
        <target namespacePrefix="GraphQL\EndpointTwo" directory="src/GraphQL/EndpointTwo" />
        <schema>
            <file name="schema-two.graphql" />
        </schema>
        <types>
            <type name="Stuff">App\Model\Stuff</type>
        </types>
        <resolvers>
            <resolver type="Query" field="stuff" />
        </resolvers>
    </endpoint>
</gqlCodegen>',
            'src'        => [
                'SchemaDirectory' => [
                    'first.graphql'  => '',
                    'second.graphql' => '',
                    'wrong.format'   => '',
                ],
            ],
        ]);

        $config = Config::fromXmlFile($root->getChild('config.xml')->url());

        self::assertEquals(
            [
                new Endpoint(
                    new Target('GraphQL\\EndpointOne', 'src/GraphQL/EndpointOne'),
                    new Schema([
                        'vfs://root/src/SchemaDirectory/first.graphql',
                        'vfs://root/src/SchemaDirectory/second.graphql',
                        'queries.graphql',
                        'mutations.graphql',
                    ]),
                    [
                        'Article' => 'App\\Model\\Article',
                        'User'    => 'App\\Model\\User',
                    ],
                    [
                        new Resolver('Query', 'users'),
                        new Resolver('Query', 'articles'),
                    ]
                ),
                new Endpoint(
                    new Target('GraphQL\\EndpointTwo', 'src/GraphQL/EndpointTwo'),
                    new Schema(['schema-two.graphql']),
                    ['Stuff' => 'App\\Model\\Stuff'],
                    [new Resolver('Query', 'stuff')]
                ),
            ],
            $config->endpoints
        );
    }
}
