<?php

namespace App\Api\Ressource\Collection;

use ApiPlatform\State\Pagination\{PaginatorInterface, HasNextPagePaginatorInterface};
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Ressource\Ressource;
use App\Dto\Ressource\RessourceDto;

final class RessourcePaginator implements \IteratorAggregate, PaginatorInterface, HasNextPagePaginatorInterface
{
    public readonly \Traversable $traversable;

    public function __construct(
        public readonly ArrayCollection $collection,
        public readonly int $currentPage,
        public readonly int $itemsPerPage,
        public readonly int $totalItems,
    ) {
        $this->traversable = $collection->map(fn(Ressource $entity) => RessourceDto::from($entity));
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPage(): float
    {
        if (0. >= $this->itemsPerPage) {
            return 1.;
        }

        return max(ceil($this->totalItems / $this->itemsPerPage) ?: 1., 1.);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        if ($this->getCurrentPage() < $this->getLastPage()) {
            return (int) ceil($this->itemsPerPage);
        }

        if (0. >= $this->itemsPerPage) {
            return (int) ceil($this->totalItems);
        }

        if ($this->totalItems === $this->itemsPerPage) {
            return (int) ceil($this->totalItems);
        }

        return $this->totalItems % $this->itemsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return $this->traversable;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNextPage(): bool
    {
        return $this->getCurrentPage() < $this->getLastPage();
    }
}
