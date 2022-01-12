<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

final class MainResolverDefinition
{
    public string $className;

    /**
     * @var list<ResolverDefinition>
     */
    public array $resolvers;

    /**
     * @param list<ResolverDefinition> $resolvers
     */
    public function __construct(string $className, array $resolvers)
    {
        $this->className = $className;
        $this->resolvers = $resolvers;
    }
}
