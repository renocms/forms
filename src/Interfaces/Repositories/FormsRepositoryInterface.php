<?php

namespace Reno\Forms\Interfaces\Repositories;

use Illuminate\Support\Collection;
use Reno\Forms\Containers\FormContainer;

interface FormsRepositoryInterface
{
    /**
     * @return Collection<int, FormContainer>
     */
    public function getAll(): Collection;

    public function findById(int $id): FormContainer;

    public function findByName(string $name): FormContainer;

    public function clearCache(): void;
}
