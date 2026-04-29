<?php

namespace Reno\Forms\Interfaces\Repositories;

use Illuminate\Support\Collection;
use Reno\Forms\Containers\ConsentContainer;

interface ConsentsRepositoryInterface
{
    /**
     * @return Collection<int, ConsentContainer>
     */
    public function getAll(): Collection;

    public function findById(int $id): ConsentContainer;

    public function findByClassName(string $className): ConsentContainer;

    public function clearCache(): void;
}
