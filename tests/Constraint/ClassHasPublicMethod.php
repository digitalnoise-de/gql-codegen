<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Exception;

final class ClassHasPublicMethod extends Constraint
{
    public function __construct(private readonly string $method)
    {
    }

    protected function matches(mixed $other): bool
    {
        try {
            $rc = new \ReflectionClass($other);

            if (!$rc->hasMethod($this->method)) {
                return false;
            }

            return $rc->getMethod($this->method)->isPublic();
        } catch (\ReflectionException $e) {
            throw new Exception($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    protected function failureDescription($other): string
    {
        return sprintf(
            '%sclass "%s" %s',
            is_object($other) ? 'object of ' : '',
            is_object($other) ? $other::class : $other,
            $this->toString()
        );
    }

    public function toString(): string
    {
        return sprintf('has public method "%s"', $this->method);
    }
}
