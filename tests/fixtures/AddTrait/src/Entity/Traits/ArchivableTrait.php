<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\fixtures\AddTrait\src\Entity\Traits;

use DateTimeInterface;

trait ArchivableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true, name="archived_at")
     */
    protected $archivedAt;

    public function getArchivedAt(): ?DateTimeInterface
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?DateTimeInterface $archivedAt): void
    {
        $this->archivedAt = $archivedAt;
    }
}
