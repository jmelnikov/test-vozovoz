<?php

namespace app\modules\api\DTO;

class TerminalDTO
{
    private array $terminals;

    /**
     * @param array $cities
     */
    public function __construct(array $cities)
    {
        $this->terminals = $cities;
    }

    /**
     * @return array
     */
    public function getTerminalsData(): array
    {
        $terminals = [];

        foreach ($this->terminals as $terminal) {
            $conditions = $this->parseConditions($terminal['conditions']['or']);

            $terminals[] = [
                'id' => $terminal['guid'],
                'name' => $terminal['name'],
                'address' => $terminal['address'],
                'note' => $terminal['note'],
                'coordinates' => $terminal['coordinates'],
                'conditions' => $conditions,
                'timetable' => $terminal['timetable'],
                'description' => $terminal['description'],
            ];
        }

        return $terminals;
    }

    private function parseConditions(array $conditions): array
    {
        $parsedConditions = [];

        foreach ($conditions as $condition) {
            if ($condition['field'] === 'cargo.total.max.width') {
                $parsedConditions['width'] = [
                    'min' => $condition['value']['from'],
                    'max' => $condition['value']['to'],
                ];
            } elseif ($condition['field'] === 'cargo.total.max.length') {
                $parsedConditions['length'] = [
                    'min' => $condition['value']['from'],
                    'max' => $condition['value']['to'],
                ];
            } elseif ($condition['field'] === 'cargo.total.max.height') {
                $parsedConditions['height'] = [
                    'min' => $condition['value']['from'],
                    'max' => $condition['value']['to'],
                ];
            }
        }

        return $parsedConditions;
    }
}
