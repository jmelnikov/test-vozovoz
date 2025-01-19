<?php

namespace app\modules\api\DTO;

class CityDTO
{
    private array $cities;

    /**
     * @param array $cities
     */
    public function __construct(array $cities)
    {
        $this->cities = $cities;
    }

    /**
     * @return array
     */
    public function getCitiesShort(): array
    {
        $cities = [];

        foreach ($this->cities as $city) {
            $cities[] = [
                'id' => $city['guid'],
                'name' => $city['name'],
                'region' => $city['region_str'],
            ];
        }

        return $cities;
    }
}
