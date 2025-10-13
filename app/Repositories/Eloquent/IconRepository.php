<?php

namespace App\Repositories\Eloquent;
use App\Repositories\Interfaces\IconRepositoryInterface;
use App\Models\Icon;

class IconRepository implements IconRepositoryInterface
{
    protected $model;

    public function __construct(Icon $icon)
    {
        $this->model = $icon;
    }

    public function all()
    {
        return $this->model->with('category', 'user')->get();
    }

    public function find(int $id)
    {
        return $this->model->with('category', 'user')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $icon = $this->find($id);
        $icon->update($data);
        return $icon;
    }

    public function delete(int $id)
    {
        $icon = $this->find($id);
        return $icon->delete();
    }
}
