<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

use DOMDocument;
use SimpleXMLElement;

/**
 * @psalm-immutable
 */
final class Config
{
    public Target $target;

    public Schema $schema;

    /**
     * @var array<string, string>
     */
    public array $types;

    /**
     * @var list<Resolver>
     */
    public array $resolvers;

    /**
     * @param array<string, string> $types
     * @param list<Resolver> $resolvers
     */
    public function __construct(Target $target, Schema $schema, array $types, array $resolvers)
    {
        $this->target    = $target;
        $this->schema    = $schema;
        $this->resolvers = $resolvers;
        $this->types     = $types;
    }

    /**
     * @throws ConfigInvalid
     */
    public static function fromXmlFile(string $filename): self
    {
        $domDocument = new DOMDocument();
        $domDocument->load($filename);

        self::validate($domDocument);

        $config = simplexml_import_dom($domDocument);

        /** @psalm-suppress MixedArgument */
        return new self(
            self::target($config->target),
            self::schema($config->schema),
            self::types($config->types),
            self::resolvers($config->resolvers)
        );
    }

    /**
     * @throws ConfigInvalid
     */
    private static function validate(DOMDocument $document): void
    {
        $schemaFile = dirname(__DIR__, 2).'/gql-codegen.xsd';

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

    private static function target(SimpleXMLElement $node): Target
    {
        return new Target((string)$node['namespacePrefix'], (string)$node['directory']);
    }

    private static function schema(SimpleXMLElement $node): Schema
    {
        $schemaFiles = [];

        foreach ($node->children() as $child) {
            $schemaFiles[] = (string)$child['name'];
        }

        return new Schema($schemaFiles);
    }

    /**
     * @return array<string, string>
     */
    private static function types(SimpleXMLElement $node): array
    {
        $types = [];

        foreach ($node->children() as $child) {
            $types[(string)$child['name']] = (string)$child;
        }

        return $types;
    }

    /**
     * @return list<Resolver>
     */
    private static function resolvers(SimpleXMLElement $node): array
    {
        $resolvers = [];

        foreach ($node->children() as $child) {
            $resolvers[] = new Resolver((string)$child['type'], (string)$child['field']);
        }

        return $resolvers;
    }
}
