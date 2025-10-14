<?php

namespace App\Repositories\Interfaces;

interface IconCategoryRepositoryInterface
{
    public function all($search = null, $rowsPerPage = 10, $page = 1);
    public function allWithoutPagination();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function changeStatus($id);
}
