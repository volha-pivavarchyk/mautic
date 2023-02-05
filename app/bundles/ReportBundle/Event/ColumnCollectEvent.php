<?php

declare(strict_types=1);

namespace Mautic\ReportBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class ColumnCollectEvent extends Event
{
    private string $object;
    private array $properties;
    private array $columns;

    public function __construct(string $object, array $properties = [])
    {
        $this->object     = $object;
        $this->properties = $properties;
        $this->columns    = [];
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addColumns(array $column): void
    {
        $this->columns = array_merge($this->columns, $column);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
