<?php


namespace App\Services\Person;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

class PersonExelService
{
    public function getAll(): JsonResponse {
        if(Storage::exists('persons.xlsx')) {
            return response()->json([
                'persons' => $this->readExel(storage_path().'/app/persons.xlsx')
            ], 200);
        }
        else {
            return response()->json([
                'persons' => [],
            ], 200);
        }
    }

    public function create($data): JsonResponse {

        $data = $data['user'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);

        if(Storage::missing('persons.xlsx')) {
            $writer->save(storage_path().'/app/persons.xlsx');
        }

        $persons = $this->readExel(storage_path().'/app/persons.xlsx');
        foreach ($persons as $key => $person) {
            if ($person['name'] == $data['name']) {
                return response()->json(['message' => 'пользователь уже существует'], 403);
            }
        }

        array_push($persons, $data);
        foreach ($persons as $key => $person) {
            $sheet->setCellValue("A".$key+1, $person['name']);
            $sheet->setCellValue("B".$key+1, $person['email']);
            $sheet->setCellValue("C".$key+1, $person['phone']);
        }
        $writer->save(storage_path().'/app/persons.xlsx');

        return $this->getAll();
    }

    public function readExel($path): array {
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $worksheetInfo = $reader->listWorksheetInfo($path);
        $totalRows = $worksheetInfo[0]['totalRows'];
        $persons = [];

        for($row = 1; $row <= $totalRows; $row++) {
            $person['name'] = $sheet->getCell("A{$row}")->getValue();
            $person['email'] = $sheet->getCell("B{$row}")->getValue();
            $person['phone'] = $sheet->getCell("C{$row}")->getValue();
            array_push($persons, $person);
        }

        return $persons;
    }
}
