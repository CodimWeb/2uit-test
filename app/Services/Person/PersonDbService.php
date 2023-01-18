<?php


namespace App\Services\Person;


use App\Models\Person;
use Illuminate\Http\JsonResponse;

class PersonDbService
{
    public function getAll(): JsonResponse {
        return response()->json([
            'persons' => Person::all(),
        ], 200);
    }

    public function create($data): JsonResponse {

        $person = Person::where('name', $data['user']['name'])->first();
        if($person) {
            return response()->json(['message' => 'пользователь уже существует'], 403);
        }
        else {
            $person = $data['user'];
            Person::create($person);
            return $this->getAll();
        }
    }
}
