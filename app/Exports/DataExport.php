<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport implements FromArray, ShouldAutoSize, WithCustomStartCell, WithDrawings, WithHeadings, WithStyles
{
    use Exportable;

    private const LOGO_ROW = 3;

    /**
     * @param  array<int, string>  $headings
     * @param  array<int, array<int, mixed>>  $rows
     */
    public function __construct(
        public readonly array $headings,
        public readonly array $rows,
        public readonly ?string $logoPath = null,
    ) {}

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function startCell(): string
    {
        return $this->logoPath ? 'A'.self::LOGO_ROW : 'A1';
    }

    public function drawings(): array
    {
        if (! $this->logoPath) {
            return [];
        }

        $drawing = new Drawing;
        $drawing->setName('Logo');
        $drawing->setDescription('Scripcom logo');
        $drawing->setPath($this->logoPath);
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');

        return [$drawing];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            ($this->logoPath ? self::LOGO_ROW : 1) => ['font' => ['bold' => true]],
        ];
    }
}
