<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Exception;
use ReflectionClass;
use ReflectionException;

final class ClassHasPublicProperty extends Constraint
{
    private string $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * @param mixed $other
     */
    protected function matches($other): bool
    {
        try {
            $rc = new ReflectionClass($other);

            if (!$rc->hasProperty($this->property)) {
                return false;
            }

            return $rc->getProperty($this->property)->isPublic();
        } catch (ReflectionException $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    protected function failureDescription($other): string
    {
        return sprintf(
            '%sclass "%s" %s',
            is_object($other) ? 'object of ' : '',
            is_object($other) ? get_class($other) : $other,
            $this->toString()
        );
    }

    public function toString(): string
    {
        return sprintf('has public property "%s"', $this->property);
    }
}
