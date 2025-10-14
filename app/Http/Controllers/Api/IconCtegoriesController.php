<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Icon\IconCategoryRequest;
use App\Http\Resources\IconCategoryResource;
use App\Repositories\Interfaces\IconCategoryRepositoryInterface;
use Illuminate\Http\Request;

class IconCtegoriesController extends Controller
{
    protected $repo;

    public function __construct(IconCategoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $data = $this->repo->all($request->search, $request->rowsPerPage, $request->page);
        return IconCategoryResource::collection($data);
    }

    public function store(IconCategoryRequest $request)
    {
        $category = $this->repo->create($request->validated());
        return response()->json([
            "success" => true,
            'message' => 'Admin login successful',
            'data' => new IconCategoryResource($category)
        ]);
    }

    public function update(IconCategoryRequest $request, $id)
    {
        $category = $this->repo->update($id, $request->validated());
        return new IconCategoryResource($category);
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }

    public function changeStatus($id)
    {
        $category = $this->repo->changeStatus($id);
        return new IconCategoryResource($category);
    }
}
