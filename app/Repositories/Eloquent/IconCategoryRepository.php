<?php

namespace App\Repositories\Eloquent;

use App\Models\IconCategories;
use App\Repositories\Interfaces\IconCategoryRepositoryInterface;

class IconCategoryRepository implements IconCategoryRepositoryInterface
{
    public function all($search = null, $perPage = 10, $page = 1)
    {
        $query = IconCategories::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderBy('id', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }

    public function allWithoutPagination()
    {
        return IconCategories::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function find($id)
    {
        return IconCategories::findOrFail($id);
    }

    public function create(array $data)
    {
        return IconCategories::create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);
        $category->delete();
        return true;
    }

    public function changeStatus($id)
    {
        $category = $this->find($id);
        $category->is_active = !$category->is_active;
        $category->save();
        return $category;
    }
}
