<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SensorReadingsExport implements FromCollection, WithHeadings
{
    protected $readings;

    // Menerima data dari resource
    public function __construct(Collection $readings)
    {
        $this->readings = $readings;
    }

    // Mengambil data untuk diekspor
    public function collection()
    {
        return $this->readings;
    }

    // Menambahkan judul kolom ke dalam file Excel
    public function headings(): array
    {
        return [
            'Sensor Name',
            'Value',
            'Measurement Time',
        ];
    }
}
