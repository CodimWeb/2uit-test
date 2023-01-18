<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonRequest;
use App\Services\Person\PersonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    private PersonService $personService;

    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
    }

    public function getAll($storageType): JsonResponse {
        return $this->personService->getAll($storageType);
    }

    public function create(PersonRequest $request): JsonResponse {
        $data = $request->validated();
        return $this->personService->create($data);
    }
}
