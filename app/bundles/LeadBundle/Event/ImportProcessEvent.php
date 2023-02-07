<?php

declare(strict_types=1);

namespace Mautic\LeadBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use Mautic\LeadBundle\Entity\Import;
use Mautic\LeadBundle\Entity\LeadEventLog;

final class ImportProcessEvent extends CommonEvent
{
    public Import $import;
    public LeadEventLog $eventLog;
    public array $rowData;
    private ?bool $wasMerged = null;
    private array $properties;

    public function __construct(Import $import, LeadEventLog $eventLog, array $rowData, $properties = [])
    {
        $this->import     = $import;
        $this->eventLog   = $eventLog;
        $this->rowData    = $rowData;
        $this->properties = $properties;
    }

    public function setWasMerged(bool $wasMerged): void
    {
        $this->wasMerged = $wasMerged;
    }

    /**
     * @throws \UnexpectedValueException
     */
    public function wasMerged(): bool
    {
        if (null === $this->wasMerged) {
            throw new \UnexpectedValueException("Import failed as {$this->import->getObject()} object is missing import handler.");
        }

        return $this->wasMerged;
    }

    public function importIsForObject(string $object): bool
    {
        return $this->import->getObject() === $object;
    }

    /**
     * @param array<string|int, mixed> $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return array<string|int, mixed>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }
}
