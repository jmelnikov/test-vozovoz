<?php

namespace app\modules\api\DTO;

class TerminalDTO
{
    private array $terminals;

    public function __construct(array $cities)
    {
        $this->terminals = $cities;
    }

    public function getTerminalsShort(): array
    {
        $terminals = [];

        foreach ($this->terminals as $terminal) {
            $conditions = [];
            foreach ($terminal['conditions']['or'] as $condition) {
                if ($condition['field'] === 'cargo.total.max.width') {
                    $conditions['width'] = [
                        'min' => $condition['value']['from'],
                        'max' => $condition['value']['to'],
                    ];
                } elseif ($condition['field'] === 'cargo.total.max.length') {
                    $conditions['length'] = [
                        'min' => $condition['value']['from'],
                        'max' => $condition['value']['to'],
                    ];
                } elseif ($condition['field'] === 'cargo.total.max.height') {
                    $conditions['height'] = [
                        'min' => $condition['value']['from'],
                        'max' => $condition['value']['to'],
                    ];
                }
            }

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
}
