<?php

namespace App\Services\Class;

use App\Models\City;
use App\Repo\Interfaces\ICityRepository;
use App\Services\Interfaces\ICityService;
use App\Traits\UploadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CityService implements ICityService
{
    use UploadImage;

    public function __construct(
        protected ICityRepository $cityRepository
    ) {}

    public function getAll()
    {
        $request = request();

        return Cache::remember(
            'cities_' . md5(json_encode($request->query())),
            now()->addHour(),
            fn() => $this->cityRepository
                ->paginate($request)
                ->through(fn($city) => $this->formatCity($city))
        );
    }

    public function getById(int $id)
    {
        return Cache::remember(
            "city_{$id}",
            now()->addHour(),
            function () use ($id) {

                $city = $this->cityRepository->getById($id);

                if (!$city) {
                    abort(404, 'City not found');
                }

                return $this->formatCity($city);
            }
        );
    }

    public function create(Request $request)
    {
        $data = $request->only([
            'country_id',
            'name',
            'description'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage(
                $request->file('image'),
                'cities'
            );
        }

        $city = $this->cityRepository->create($data);

        Cache::flush();

        return $this->formatCity($city->load(['country', 'attractions']));
    }

    public function update(int $id, Request $request)
    {
        $city = $this->cityRepository->getById($id);

        if (!$city) {
            abort(404, 'City not found');
        }

        $data = $request->only([
            'country_id',
            'name',
            'description'
        ]);

        if ($request->hasFile('image')) {

            $this->deleteImage($city->image);

            $data['image'] = $this->uploadImage(
                $request->file('image'),
                'cities'
            );
        }

        $city = $this->cityRepository->update($id, $data);

        Cache::flush();

        return $this->formatCity($city);
    }

    public function delete(int $id)
    {
        $city = $this->cityRepository->getById($id);

        if (!$city) {
            abort(404, 'City not found');
        }

        $this->deleteImage($city->image);

        $this->cityRepository->delete($id);

        Cache::flush();

        return [
            'message' => 'City deleted successfully'
        ];
    }

    private function formatCity(City $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name,
            'description' => $city->description,
           'image' => $city->image
    ? (str_starts_with($city->image, 'http')
        ? $city->image
        : url(Storage::url($city->image)))
    : null,
            'country' => [
                'name' => $city->country?->name,
                'official_name' => $city->country?->official_name,
                'code2' => $city->country?->code2,
                'flag' => [
                    'png' => $city->country?->flag,
                ],
            ],

            'attractions' => $city->attractions->map(function ($attraction) {
                return [
                    'id' => $attraction->id,
                    'name' => $attraction->name,
                    'description' => $attraction->description,
                  'image' => $attraction->image
    ? (str_starts_with($attraction->image, 'http')
        ? $attraction->image
        : url(Storage::url($attraction->image)))
    : null,
                ];
            })->values(),
        ];
    }
}