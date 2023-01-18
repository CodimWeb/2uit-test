<?php


namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Http\JsonResponse;


class PersonService
{
    private PersonDbService $personDbService;
    private PersonCacheService $personCacheService;
    private PersonJsonService $personJsonService;
    private PersonExelService $personExelService;

    public function __construct(
        PersonDbService $personDbService,
        PersonCacheService $personCacheService,
        PersonJsonService $personJsonService,
        PersonExelService $personExelService
    )
    {
        $this->personDbService = $personDbService;
        $this->personCacheService = $personCacheService;
        $this->personJsonService = $personJsonService;
        $this->personExelService = $personExelService;
    }

    public function getAll($storageType): JsonResponse {
        switch ($storageType) {
            case 'cache':
                return $this->personCacheService->getAll();
            case 'json':
                return $this->personJsonService->getAll();
            case 'xlsx':
                return $this->personExelService->getAll();
            default:
                return $this->personDbService->getAll();
        }
    }

    public function create($data): JsonResponse {
        $storageType = $data['storageType'];
        switch ($storageType) {
            case 'db':
                return $this->personDbService->create($data);
            case 'cache':
                return $this->personCacheService->create($data);
            case 'json':
                return $this->personJsonService->create($data);
            case 'xlsx':
                return $this->personExelService->create($data);
            default:
                return response()->json(['message' => 'Источник данных не поддерживается'], 403);

        }
    }
}
