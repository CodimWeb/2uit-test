<?php


namespace App\Services\Person;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PersonCacheService
{
    public function getAll(): JsonResponse {
//        Redis::command('DEL', ['persons']);
        $persons = Redis::command('HVALS', ['persons']);
        foreach ($persons as $key => $value) {
            $persons[$key] = json_decode($value);
        }
        return response()->json([
            'persons' => $persons
        ], 200);
    }

    public function create($data): JsonResponse {
        $person = json_encode($data['user']);
        $key = json_encode($data['user']['name']);

        $personsKeys = Redis::command('HKEYS', ['persons']);

        if(in_array($key, $personsKeys)) {
            return response()->json(['message' => 'пользователь уже существует'], 403);
        }
        else {
            Redis::command('HSET', ['persons', $key, $person]);
            return $this->getAll();
        }
    }
}
