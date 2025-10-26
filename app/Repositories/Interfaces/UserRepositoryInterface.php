<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getAllUsers($search = null, $rowsPerPage = 10, $page = 1);
    public function all();
    public function find($id): ?User;
    public function create(array $data): User;
    public function update(User $user, array $data): User;
    public function delete(User $user): bool;
    public function login(array $credentials, ?string $role = null): ?User;
}
