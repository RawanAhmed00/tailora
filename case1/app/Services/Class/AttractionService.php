<?php

namespace App\Services\Class;

use App\Models\Attraction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\Interfaces\IAttractionService;
use App\Repo\Interfaces\IAttractionRepository;

class AttractionService implements IAttractionService
{
    public function __construct(
        protected IAttractionRepository $repository
    ) {}

public function getAll(int $perPage = 10)
{
    return $this->repository->getAll($perPage);
}

    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    public function create(Request $request)
    {
        $data = $request->only([
            'city_id',
            'name',
            'description',
            'latitude',
            'longitude',
            'price'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('attractions', 'public');
        }

        $attraction = $this->repository->create($data);

        if ($request->has('categories')) {
            $attraction->categories()->sync($request->categories);
        }

        return $attraction->load(['city', 'categories']);
    }

    public function update(Request $request, int $id)
    {
        $attraction = Attraction::find($id);

        if (!$attraction) {
            return null;
        }

        $data = $request->only([
            'city_id',
            'name',
            'description',
            'latitude',
            'longitude',
            'price'
        ]);

        if ($request->hasFile('image')) {

            if ($attraction->image) {
                Storage::disk('public')->delete($attraction->image);
            }

            $data['image'] = $request->file('image')->store('attractions', 'public');
        }

        $this->repository->update($id, $data);

        if ($request->has('categories')) {
            $attraction->categories()->sync($request->categories);
        }

        return $attraction->load(['city', 'categories']);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}