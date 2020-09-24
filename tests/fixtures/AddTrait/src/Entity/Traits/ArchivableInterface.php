<?php

declare(strict_types=1);

namespace DH\ArtisPackageManagerBundle\Tests\fixtures\AddTrait\src\Entity\Traits;

use DateTimeInterface;

interface ArchivableInterface
{
    public function getArchivedAt(): ?DateTimeInterface;

    public function setArchivedAt(?DateTimeInterface $archivedAt): void;
}
