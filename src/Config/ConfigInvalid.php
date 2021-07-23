<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

use Exception;

final class ConfigInvalid extends Exception
{
    public static function onLine(int $line, string $error): self
    {
        return new self(sprintf('Error on line %d: %s', $line, $error));
    }
}
