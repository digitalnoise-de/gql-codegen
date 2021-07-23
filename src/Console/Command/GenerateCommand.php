<?php
declare(strict_types=1);

namespace GraphQLGenerator\Console\Command;

use GraphQLGenerator\Config\Config;
use GraphQLGenerator\Generator\ClassGenerator;
use GraphQLGenerator\Processor;
use GraphQLGenerator\Psr4ClassDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('generate');
        $this->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'Configuration file', 'gql-codegen.xml');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = Config::fromXmlFile('gql-codegen.xml');

        $classDumper = new Psr4ClassDumper($config->target->namespacePrefix, $config->target->directory);

        $buildDefinition = (new Processor())->process($config);

        $classGenerator = ClassGenerator::forPhp74();

        foreach ($buildDefinition->inputTypes as $inputType) {
            $output->writeln(sprintf('- Generating %s', $inputType->className));
            $classDumper->dump($classGenerator->inputType($inputType));
        }

        foreach ($buildDefinition->resolvers as $resolver) {
            $output->writeln(printf('- Generating %s', $resolver->className));
            $classDumper->dump($classGenerator->resolver($resolver));
        }

        $classDumper->dump($classGenerator->mainResolver($buildDefinition->mainResolver));

        return Command::SUCCESS;
    }
}
