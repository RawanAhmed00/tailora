<?php

namespace App\Services\Interfaces;

interface ICountryService
{
    public function getCountries(array $filters = []);

    public function search(string $query);

    public function getByName(string $name);

    public function getByRegion(string $region);
     public function getCountryByCode(string $code);
}