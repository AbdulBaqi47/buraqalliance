<?php

namespace App\Imports\Helper;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class PreviewData implements WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    /**
     * @param  int  $headingRow
     */
    public function __construct()
    {
        HeadingRowFormatter::default('none');
    }

}
