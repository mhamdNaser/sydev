<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $cities = $this->cityRepository->getAllCities();
            return CitiesResource::collection($cities);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve cities.'], 500);
        }
    }

    public function allcities(Request $request, $id)
    {
        $search = $request->get('search');
        $rowsPerPage = $request->get('rowsPerPage', 10);
        $page = $request->get('page', 1);

        $cities = $this->cityRepository->getAllCitiesByStateId($id);

        $filteredcity = $cities->filter(function ($city) use ($search) {
            return strpos(strtolower($city->name), strtolower($search)) !== false;
        });


        $currentPage = $page;
        $perPage = $rowsPerPage;
        $offset = ($currentPage - 1) * $perPage;

        $pagedcity = $filteredcity->slice($offset, $perPage)->values();


        $paginator = new LengthAwarePaginator(
            $pagedcity,
            $filteredcity->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'data' => AllCitiesResource::collection($paginator->items()),
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->cityRepository->deleteCity($id);
            return response()->json([
                'success' => true,
                'message' => 'City deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete city.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroyarray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        $idsToDelete = $validatedData['array'];

        try {
            $this->cityRepository->deleteCities($idsToDelete);
            return response()->json([
                'success' => true,
                'message' => 'Cities deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cities.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
