<?php

namespace App\Exports;

use App\DamageReport;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DamageReportsExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    public string $fileName = 'laporan-kerusakan.xlsx';

    public function __construct(private $query)
    {
    }

    public function collection()
    {
        return $this->query->get();
    }

    public function headings(): array
    {
        return ['Judul', 'Barang', 'Pelapor', 'Keparahan', 'Status', 'Dibuat'];
    }

    public function map($report): array
    {
        return [
            $report->title,
            $report->commodity->name ?? '-',
            $report->reporter->name ?? '-',
            ucfirst($report->severity),
            ucfirst($report->status),
            optional($report->created_at)->format('Y-m-d'),
        ];
    }
}

