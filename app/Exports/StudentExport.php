<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudentExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Student::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'NIK',
            'No KK',
            'NIM',
            'Nama',
            'NIK Valid',
            'Valid Univ',
            'Status',
            'Deskripsi Status',
            'Status ID',
            'Nama PT',
            'Nama Prodi',
            'Jenjang',
            'Jalur Masuk',
            'Max SPP',
            'SPP Ditanggung',
            'Finalized At',
            'Notes',
            'Batch Accepted',
            'Created At',
            'Updated At',
        ];
    }
}
