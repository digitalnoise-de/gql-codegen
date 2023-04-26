<?php
declare(strict_types=1);

namespace GraphQLGenerator\Build;

final class BuildDefinition
{
    /**
     * @param list<InputTypeDefinition> $inputTypes
     * @param list<ResolverDefinition>  $resolvers
     */
    public function __construct(
        public readonly array $inputTypes,
        public readonly array $resolvers,
        public readonly MainResolverDefinition $mainResolver
    ) {
    }
}
