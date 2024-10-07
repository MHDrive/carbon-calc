<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use App\Models\Recapitulation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecapitulationExport implements FromView, ShouldAutoSize
// FromArray
{
    // use Exportable;

    protected $column;
    protected $record;
    
    public function __construct($column, $record)
    {
        $this->column = $column;
        $this->record = $record;
    }
    public function view(): View
    {
        return view('exports.recapitulation-export', [
            'record' => $this->record,
            'columns' => $this->column,
        ]);
    }
}
