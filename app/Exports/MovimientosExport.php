<?php

namespace App\Exports;

use App\Models\Movimiento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovimientosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $movimientos;

    public function __construct($movimientos)
    {
        $this->movimientos = $movimientos;
    }

    public function collection()
    {
        return $this->movimientos;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Categoría',
            'Tipo',
            'Monto',
            'Descripción'
        ];
    }

    public function map($movimiento): array
    {
        return [
            $movimiento->fecha->format('Y-m-d'),
            $movimiento->categoria->nombre ?? '',
            ucfirst($movimiento->tipo),
            $movimiento->monto,
            $movimiento->descripcion ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
