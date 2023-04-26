<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

final class Endpoint
{
    /**
     * @param array<string, string> $types
     * @param list<Resolver>        $resolvers
     */
    public function __construct(
        public readonly Target $target,
        public readonly Schema $schema,
        public readonly array $types,
        public readonly array $resolvers
    ) {
    }
}
