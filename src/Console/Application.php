<?php
declare(strict_types=1);

namespace GraphQLGenerator\Console;

use GraphQLGenerator\Console\Command\GenerateCommand;
use Symfony\Component\Console\Application as BaseApplication;

final class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('gql-codegen');

        $this->setDefaultCommand('generate');

        $this->add(new GenerateCommand());
    }
}
