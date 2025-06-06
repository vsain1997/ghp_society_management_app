<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SocietyImport implements ToCollection,WithHeadingRow
{
    public $data;
   public function collection(Collection $rows)
    {
        $flattenedData = [];

        foreach ($rows as $row) {
            $blockName = trim($row['block_name'] ?? '');
            $totalUnit = trim($row['total_unit'] ?? '');
            $propertyDetailsRaw = trim($row['propery_details'] ?? '');

            $properties = [];

            // Separate each property line by line (split by newline)
            $propertyLines = preg_split('/\r\n|\r|\n/', $propertyDetailsRaw);

            foreach ($propertyLines as $line) {
                $props = array_map('trim', explode(',', $line));
                if (count($props) <= 5) {
                    $properties[] = [
                        'property_number' => $props[0],
                        'floor'           => $props[1],
                        'type'            => $props[2],
                        'size'            => $props[3],
                        'bhk'             => $props[4] ?? null,
                    ];
                }
            }
            
            $flattenedData[] = [
                'block_name'      => $blockName,
                'total_unit'      => $totalUnit,
                'property_number' => $properties,
            ];
        }
        // dd($flattenedData);

        $this->data = collect($flattenedData);
    }

}
