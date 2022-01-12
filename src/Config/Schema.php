<?php
declare(strict_types=1);

namespace GraphQLGenerator\Config;

final class Schema
{
    /**
     * @var list<string>
     */
    private array $files;

    /**
     * @param list<string> $files
     */
    public function __construct(array $files)
    {
        $this->files = $files;
    }

    public function content(): string
    {
        $content = '';

        foreach ($this->files as $file) {
            $content .= file_get_contents($file);
        }

        return $content;
    }
}
