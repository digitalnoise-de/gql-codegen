<?php
declare(strict_types=1);

namespace Tests\GraphQLGenerator;

use Generator;
use GraphQLGenerator\Generator\GeneratedClass;
use GraphQLGenerator\Psr4ClassDumper;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GraphQLGenerator\Psr4ClassDumper
 */
final class Psr4ClassDumperTest extends TestCase
{
    private vfsStreamDirectory $root;

    private Psr4ClassDumper $subject;

    /**
     * @test
     *
     * @dataProvider invalidClassNameExamples
     */
    public function invalid_class_names(string $class, string $expectedException): void
    {
        self::expectExceptionMessage($expectedException);

        $this->subject->dump(new GeneratedClass($class, ''));
    }

    /**
     * @return Generator<string, array{0: string, 1: string}>
     */
    public function invalidClassNameExamples(): Generator
    {
        yield 'No namespace' => [
            'Foo',
            'Class "Foo" does not belong to namespace "Acme\\GraphQL"'
        ];

        yield 'Case mismatch' => [
            'Acme\\Graphql',
            'Class "Acme\\Graphql" does not belong to namespace "Acme\\GraphQL"'
        ];

        yield 'No class name' => [
            'Acme\\GraphQL',
            'Class "Acme\\GraphQL" can not be created in namespace "Acme\\GraphQL"'
        ];
    }

    /**
     * @test
     */
    public function file_should_be_created(): void
    {
        $this->subject->dump(new GeneratedClass('Acme\\GraphQL\\Test', 'class-content'));

        /** @var vfsStreamFile $vfsFile */
        $vfsFile = $this->root->getChild('Test.php');
        self::assertFileExists($vfsFile->url());
        self::assertEquals('class-content', $vfsFile->getContent());
    }

    /**
     * @test
     */
    public function directories_should_be_created(): void
    {
        $this->subject->dump(new GeneratedClass('Acme\\GraphQL\\Resolver\\Foo\\Test', 'class-content'));

        /** @var vfsStreamDirectory $vfsFile */
        $vfsFile = $this->root->getChild('Resolver')->getChild('Foo')->getChild('Test.php');
        self::assertFileExists($vfsFile->url());
        self::assertEquals('class-content', $vfsFile->getContent());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = vfsStream::setup();

        $this->subject = new Psr4ClassDumper('Acme\\GraphQL', $this->root->url());
    }
}
