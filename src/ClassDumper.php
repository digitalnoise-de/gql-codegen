<?php
declare(strict_types=1);

namespace GraphQLGenerator;

use GraphQLGenerator\Generator\GeneratedClass;

interface ClassDumper
{
    public function dump(GeneratedClass $class): void;
}
