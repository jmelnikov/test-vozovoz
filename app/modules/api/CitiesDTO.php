<?php

namespace app\modules\api;

class CitiesDTO
{
    private array $cities;

    public function __construct(array $cities)
    {
        $this->cities = $cities;
    }

    public function getCitiesShort(): array
    {
        $cities = [];

        foreach ($this->cities as $city) {
            $cities[] = [
                'id' => $city['guid'],
                'name' => $city['name'],
            ];
        }

        return $cities;
    }
}
