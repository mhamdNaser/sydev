<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Icon\StoreIconRequest;
use App\Http\Resources\IconResource;
use App\Repositories\Interfaces\IconRepositoryInterface;
use Illuminate\Http\Request;

class IconController extends Controller
{
    protected $repo;

    public function __construct(IconRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $rowsPerPage = $request->input('rowsPerPage', 10);
        $page = $request->input('page', 1);

        $result = $this->repo->all($search, $rowsPerPage, $page);

        return response()->json([
            'data' => IconResource::collection($result['data']),
            'meta' => $result['meta'],
            'links' => $result['links'],
        ]);
    }

    public function store(StoreIconRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $icon = $this->repo->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Icon created successfully',
            'data' => $icon
        ], 201);
    }

    public function update(StoreIconRequest $request, $id)
    {
        $data = $request->validated();
        $icon = $this->repo->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Icon updated successfully',
            'data' => $icon
        ]);
    }

    public function destroy($id)
    {
        $this->repo->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Icon deleted successfully'
        ]);
    }

    public function changeStatus($id)
    {
        $icon = $this->repo->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => 'Status changed successfully',
            'data' => $icon
        ]);
    }
}
