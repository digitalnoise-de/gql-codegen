<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

use Symfony\Component\Finder\Finder;

/**
 * @psalm-immutable
 */
final class Config
{
    /**
     * @param list<Endpoint> $endpoints
     */
    public function __construct(public readonly array $endpoints)
    {
    }

    /**
     * @throws ConfigInvalid
     */
    public static function fromXmlFile(string $filename): self
    {
        $domDocument = new \DOMDocument();
        $domDocument->load($filename);

        self::validate($domDocument);

        $config = simplexml_import_dom($domDocument);

        if (!$config instanceof \SimpleXMLElement) {
            throw new \RuntimeException(sprintf('Error loading configuration "%s"', $filename));
        }

        return new self(self::endpoints($config));
    }

    /**
     * @throws ConfigInvalid
     */
    private static function validate(\DOMDocument $document): void
    {
        $schemaFile = dirname(__DIR__, 2) . '/gql-codegen.xsd';

        libxml_use_internal_errors(true);

        if (!$document->schemaValidate($schemaFile)) {
            foreach (libxml_get_errors() as $error) {
                if ($error->level === LIBXML_ERR_FATAL || $error->level === LIBXML_ERR_ERROR) {
                    throw ConfigInvalid::onLine($error->line, $error->message);
                }
            }
            libxml_clear_errors();
        }
    }

    /**
     * @return list<Endpoint>
     */
    private static function endpoints(\SimpleXMLElement $node): array
    {
        $endpoints = [];

        foreach ($node->children() ?? [] as $child) {
            $endpoints[] = self::endpoint($child);
        }

        return $endpoints;
    }

    private static function endpoint(\SimpleXMLElement $node): Endpoint
    {
        /** @psalm-suppress MixedArgument */
        return new Endpoint(
            self::target($node->target),
            self::schema($node->schema),
            self::types($node->types),
            self::resolvers($node->resolvers)
        );
    }

    private static function target(\SimpleXMLElement $node): Target
    {
        return new Target((string)$node['namespacePrefix'], (string)$node['directory']);
    }

    private static function schema(\SimpleXMLElement $node): Schema
    {
        $schemaFiles = [];

        foreach ($node->children() ?? [] as $key => $child) {
            if ($key === 'directory') {
                $schemaFiles = [...$schemaFiles, ...self::directorySchemaFiles($child)];

                continue;
            }

            $schemaFiles[] = (string)$child['name'];
        }

        return new Schema($schemaFiles);
    }

    /**
     * @return array<string, string>
     */
    private static function types(\SimpleXMLElement $node): array
    {
        $types = [];

        foreach ($node->children() ?? [] as $child) {
            $types[(string)$child['name']] = (string)$child;
        }

        return $types;
    }

    /**
     * @return list<Resolver>
     */
    private static function resolvers(\SimpleXMLElement $node): array
    {
        $resolvers = [];

        foreach ($node->children() ?? [] as $child) {
            $resolvers[] = new Resolver((string)$child['type'], (string)$child['field']);
        }

        return $resolvers;
    }

    /**
     * @return list<string>
     */
    private static function directorySchemaFiles(\SimpleXMLElement $child): array
    {
        $directory = (string)$child['name'];
        $finder    = new Finder();
        $finder->files()->name('*.graphql')->in($directory);

        return array_map(fn (\SplFileInfo $fileInfo) => $fileInfo->getPathname(), iterator_to_array($finder, false));
    }
}
