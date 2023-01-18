<?php


namespace App\Services\Person;


use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PersonJsonService
{
    public function getAll(): JsonResponse {
        if(Storage::exists('persons.json')) {
            return response()->json([
                'persons' => json_decode(Storage::get('persons.json')),
            ], 200);
        }
        else {
            return response()->json([
                'persons' => [],
            ], 200);
        }


    }

    public function create($data): JsonResponse {
        if(!Storage::exists('persons.json')) {
            Storage::put('persons.json', '[]');
        }

        $person = $data['user'];
        $persons = json_decode(Storage::get('persons.json'));

        foreach ($persons as $item) {
            if ($item->name == $person['name']) {
                return response()->json(['message' => 'пользователь уже существует'], 403);
            }
        }

        array_push($persons, $person);
        Storage::put('persons.json', json_encode($persons));
        return $this->getAll();
    }
}
