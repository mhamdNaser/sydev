<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index()
    {
        return $this->countryRepository->getAllCountries();
    }

    public function allCountry(Request $request)
    {
        $search = $request->get('search');
        $rowsPerPage = $request->get('rowsPerPage', 10);
        $page = $request->get('page', 1);

        $allcountry = $this->countryRepository->getAllCountries();

        $filteredCountry = $allcountry->filter(function ($country) use ($search) {
            return strpos(strtolower($country->name), strtolower($search)) !== false;
        });


        $currentPage = $page;
        $perPage = $rowsPerPage;
        $offset = ($currentPage - 1) * $perPage;

        $pagedcountry = $filteredCountry->slice($offset, $perPage)->values();


        $paginator = new LengthAwarePaginator(
            $pagedcountry,
            $filteredCountry->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'data' => CountriesResource::collection($paginator->items()),
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

    public function store(StoreRequest $request)
    {
        return $this->countryRepository->storeCountry($request);
    }

    public function destroy($id)
    {
        return $this->countryRepository->deleteCountry($id);
    }

    public function destroyarray(Request $request)
    {
        $validatedData = $request->validate([
            'array' => 'required|array',
        ]);

        return $this->countryRepository->deleteCountries($validatedData['array']);
    }
}
