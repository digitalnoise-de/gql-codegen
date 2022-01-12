<?php
declare(strict_types=1);

namespace GraphQLGenerator\Type;

final class ExistingClassType implements Type
{
    /**
     * @var string
     */
    public string $className;

    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function __toString(): string
    {
        return sprintf('ExistingClass<%s>', $this->className);
    }
}
