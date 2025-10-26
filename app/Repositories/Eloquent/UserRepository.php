<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Traits\PaginatesCollection;

class UserRepository implements UserRepositoryInterface
{
    use PaginatesCollection;

    public function getAllUsers($search = null, $rowsPerPage = 10, $page = 1)
    {
        $cacheKey = "all_Users";

        $items = Cache::remember($cacheKey, 60, function () {
            return User::orderBy('id', 'desc')->get();
        });

        if ($search) {
            $items = $items->filter(function ($item) use ($search) {
                return stripos($item->name, $search) !== false;
            });
        }
        return $this->paginate($items, $rowsPerPage, $page);
    }

    public function all()
    {
        return User::latest()->get();
    }

    public function find($id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function login(array $credentials, ?string $role = null): ?User
    {
        $query = User::query();

        if ($role === 'admin') {
            $query->admin();
        } elseif ($role === 'user') {
            $query->user();
        }

        $user = $query->where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }

        return null;
    }
}
