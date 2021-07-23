<?php
declare(strict_types=1);

namespace GraphQLGenerator;

final class BuildDefinition
{
    /**
     * @var list<InputTypeDefinition>
     */
    public array $inputTypes = [];

    /**
     * @var list<ResolverDefinition>
     */
    public array $resolvers = [];

    public MainResolverDefinition $mainResolver;

    /**
     * @param list<InputTypeDefinition> $inputTypes
     * @param list<ResolverDefinition>  $resolvers
     */
    public function __construct(array $inputTypes, array $resolvers, MainResolverDefinition $mainResolver)
    {
        $this->inputTypes   = $inputTypes;
        $this->resolvers    = $resolvers;
        $this->mainResolver = $mainResolver;
    }
}
