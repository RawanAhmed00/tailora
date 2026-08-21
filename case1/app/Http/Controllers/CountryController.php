<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    protected ICountryService $countryService;

    public function __construct(ICountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    /**
     * Get all countries from database.
     */
    public function index(Request $request)
    {
        return response()->json(
            $this->countryService->getCountries($request->all())
        );
    }

    /**
     * Search countries from database.
     */
    public function search(Request $request)
    {
        $q = $request->input('q') ?? $request->input('query') ?? $request->input('search') ?? '';

        return response()->json(
            $this->countryService->search((string)$q)
        );
    }

    /**
     * Get country details by name, code, or ID from database.
     */
    public function show(string $name)
    {
        $country = $this->countryService->getByName($name);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        return response()->json($country);
    }

    /**
     * Get countries by region from database.
     */
    public function region(string $region)
    {
        return response()->json(
            $this->countryService->getByRegion($region)
        );
    }
}