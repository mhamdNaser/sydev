<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Icon\StoreIconRequest;
use App\Repositories\Interfaces\IconRepositoryInterface;
use Illuminate\Http\Request;

class IconController extends Controller
{
    protected $repo;

    public function __construct(IconRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        return response()->json($this->repo->all());
    }

    public function show($id)
    {
        return response()->json($this->repo->find($id));
    }

    public function store(StoreIconRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $icon = $this->repo->create($data);
        return response()->json($icon, 201);
    }

    public function update(StoreIconRequest $request, $id)
    {
        $data = $request->validated();
        $icon = $this->repo->update($id, $data);
        return response()->json($icon);
    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Icon deleted successfully']);
    }
}
