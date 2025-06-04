<?php

namespace App\Imports;

use App\Models\Society;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Imports\MembersImport;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class SocietyImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        dd($rows);
    }
}
