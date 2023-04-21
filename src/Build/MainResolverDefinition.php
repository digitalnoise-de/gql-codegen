<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

final class MainResolverDefinition
{
    /**
     * @param list<ResolverDefinition> $resolvers
     */
    public function __construct(public readonly string $className, public readonly array $resolvers)
    {
    }
}
