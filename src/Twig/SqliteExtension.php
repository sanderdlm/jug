<?php

namespace Jug\Twig;

use Jug\Exception\FileSystemException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SqliteExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('data', [$this, 'loadData']),
        ];
    }

    /**
     * @return array<mixed>|null
     */
    public function loadData(string $databaseFile, string $query): ?array
    {
        if (!file_exists($databaseFile)) {
            throw FileSystemException::missingFile($databaseFile);
        }

        $database = new \PDO("sqlite:$databaseFile");

        $database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $result = $database->query($query);

        if ($result) {
            return $result->fetchAll();
        }

        return null;
    }
}
