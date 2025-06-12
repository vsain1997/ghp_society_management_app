<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SocietyImport implements ToCollection,WithHeadingRow
{
    public $data;
    public $totalTowers = 0;
    public $societyId = 0;

    public function __construct($societyId, $totalTowers)
    {
        $this->societyId = $societyId;
        $this->totalTowers = $totalTowers;
    }

    public function collection(Collection $rows){
    $groupedData = [];

    foreach ($rows as $row) {
        $blockName = trim($row['block_name'] ?? '');
        if (!$blockName) continue; // skip if block name is empty

        // Prepare property detail
            $property = [
                'property_number' => trim($row['property_number'] ?? ''),
                'floor'           => trim($row['floor'] ?? ''),
                'type'            => trim($row['type'] ?? ''),
                'area'            => trim($row['area'] ?? ''),
                'bhk'             => trim($row['bhk'] ?? ''),
            ];

            // Initialize block if not already
            if (!isset($groupedData[$blockName])) {
                $groupedData[$blockName] = [
                    'block_name' => $blockName,
                    'properties' => [],
                ];
            }

            // Append property to the respective block
            $groupedData[$blockName]['properties'][] = $property;
        }
        // Optional: Re-index the array (if you want a clean list instead of associative keys)
        $flattenedData = array_values($groupedData);
        dd($flattenedData);

        // Store the flattened data into the class property
        $this->data = collect($flattenedData);
    }


}
