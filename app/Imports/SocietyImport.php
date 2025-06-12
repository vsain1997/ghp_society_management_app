<?php

namespace App\Imports;
use Illuminate\Http\JsonResponse;

use App\Models\Block;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SocietyImport implements ToCollection,WithHeadingRow
{
    public $data;
    public $totalTowers = 0;
    public $societyId = 0;
    public $unitType = '';
    public function __construct($societyId, $totalTowers,$unitType)
    {
        $this->societyId = $societyId;
        $this->totalTowers = $totalTowers;
        $this->unitType = $unitType;
    }

   public function collection(Collection $rows): JsonResponse
{
    $groupedData = [];
    $skippedBlocks = 0;

    foreach ($rows as $row) {
        $blockName = trim($row['block_name'] ?? '');

        // Skip and count rows with empty block_name
        if (!$blockName) {
            continue;
        }

        $property = [
            'property_number' => trim($row['property_number'] ?? ''),
            'floor'           => trim($row['floor'] ?? ''),
            'type'            => trim($row['type'] ?? ''),
            'area'            => trim($row['area'] ?? ''),
            'bhk'             => trim($row['bhk'] ?? ''),
        ];

        if (!isset($groupedData[$blockName])) {
            $groupedData[$blockName] = [
                'block_name' => $blockName,
                'properties' => [],
            ];
        }

        $groupedData[$blockName]['properties'][] = $property;
    }

    $flattenedData = array_values($groupedData);
    if(count($flattenedData) > $this->totalTowers) {       
        $skippedBlocks = count($flattenedData) - $this->totalTowers;
    }
    
    $chunkSize = 100;
    $insertedCount = 0;

    foreach (array_chunk(array_slice($flattenedData, 0, $this->totalTowers), $chunkSize) as $blockChunk) {
        $bulkInsert = [];

        foreach ($blockChunk as $block) {
            foreach ($block['properties'] as $property) {
                $bulkInsert[] = [
                    'society_id'      => $this->societyId,
                    'name'            => $block['block_name'],
                    'property_number' => $property['property_number'],
                    'floor'           => $property['floor'],
                    'unit_size'       => $property['area'],
                    'bhk'             => $property['bhk'],
                    'unit_type'       => $this->unitType,
                    'total_units'     => 0,
                ];
            }
        }

        Block::upsert(
            $bulkInsert,
            ['society_id', 'name', 'property_number'],
            ['floor', 'unit_size', 'bhk', 'unit_type', 'total_units']
        );

        $insertedCount += count($bulkInsert);
    }
    $responseData = [
        'status' => true,
        'message' => 'Blocks & Properties imported successfully!',
        'inserted_records' => $insertedCount,
        'total_blocks' => count($flattenedData),
        'skipped_blocks' => $skippedBlocks,
    ];

    // Store in session
    session()->put('import_response', $responseData);

}
}
