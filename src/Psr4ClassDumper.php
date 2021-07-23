<?php
declare(strict_types=1);

namespace GraphQLGenerator;

use Exception;
use GraphQLGenerator\Generator\GeneratedClass;

final class Psr4ClassDumper implements ClassDumper
{
    private string $prefix;

    private string $directory;

    public function __construct(string $prefix, string $directory)
    {
        $this->prefix    = $prefix;
        $this->directory = $directory;
    }

    public function dump(GeneratedClass $class): void
    {
        $len = strlen($this->prefix);
        if (strncmp($this->prefix, $class->name, $len) !== 0) {
            throw new Exception(sprintf('Class "%s" does not belong to namespace "%s"', $class->name, $this->prefix));
        }

        $relativeClass = substr($class->name, $len);
        if ($relativeClass === '') {
            throw new Exception(
                sprintf('Class "%s" can not be created in namespace "%s"', $class->name, $this->prefix)
            );
        }

        $file = sprintf('%s%s.php', $this->directory, str_replace('\\', '/', $relativeClass));
        $path = dirname($file);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($file, $class->content);
    }
}
