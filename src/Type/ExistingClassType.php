<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class ExistingClassType implements Type
{
    /**
     * @var class-string
     */
    public string $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }
}
