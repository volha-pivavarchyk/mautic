<?php

namespace Mautic\FormBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MappedObjectColumnEvent extends Event
{
    private array $mappedObjects;
    private array $mappedObjectColumns;

    public function __construct(array $mappedObjects, array $mappedObjectColumns = [])
    {
        $this->mappedObjects       = $mappedObjects;
        $this->mappedObjectColumns = $mappedObjectColumns;
    }

    public function getMappedObjects(): array
    {
        return $this->mappedObjects;
    }

    public function appendMappedObjectColumns(array $mappedObjectColumns): void
    {
        $this->mappedObjectColumns = array_merge($this->mappedObjectColumns, $mappedObjectColumns);
    }
    public function getMappedObjectColumns(): array
    {
        return $this->mappedObjectColumns;
    }
}
