<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use tFPDF;

class ReportPdfService extends tFPDF
{
    private const MARGIN_X = 12;

    private const BOTTOM_MARGIN = 14;

    private const HEADER_ROW_HEIGHT = 6.5;

    private const LINE_HEIGHT = 4.4;

    private const NAVY = [29, 39, 51];

    private const GRAY = [107, 114, 128];

    private const BODY_TEXT = [26, 26, 26];

    private const LIGHT_GRAY = [229, 231, 235];

    private const ZEBRA = [246, 247, 249];

    private const FOOTER_GRAY = [156, 163, 175];

    /**
     * Reports whose columns are too wide for a portrait page are rendered
     * landscape. Anything not listed here defaults to portrait.
     */
    private const ORIENTATIONS = [
        'sessions' => 'L',
        'events' => 'L',
        'hotspots' => 'L',
        'sponsorships' => 'L',
    ];

    /**
     * Preferred column widths (mm) per report type, in column order. Values
     * are scaled to the content width.
     */
    private const WIDTHS = [
        'usage' => [45, 42, 52, 42, 50, 50],
        'sessions' => [26, 33, 33, 16, 24, 24, 27, 25, 18, 18, 18, 12],
        'events' => [42, 34, 30, 30, 30, 110],
        'payments' => [38, 38, 32, 22, 18, 25, 40, 62],
        'hotspots' => [45, 30, 30, 30, 30, 18, 12, 18, 36, 26],
        'campaigns' => [46, 36, 25, 20, 20, 20, 26, 42, 42],
        'sponsorships' => [30, 30, 20, 20, 16, 16, 22, 22, 22, 16, 20, 28, 28],
    ];

    /**
     * @var array<string, mixed>
     */
    private array $definition = [];

    /**
     * @var array<string, mixed>
     */
    private array $filters = [];

    private int $rowCount = 0;

    private \DateTimeInterface $generatedAt;

    /**
     * @var array<int, float>
     */
    private array $widths = [];

    private ?string $logoPath = null;

    private string $appName = '';

    private string $orientation = 'P';

    private float $contentWidth = 186;

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $filters
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function render(string $type, array $definition, array $filters, array $rows, ?string $sourceLogo = null): string
    {
        $this->definition = $definition;
        $this->filters = $filters;
        $this->rowCount = count($rows);
        $this->generatedAt = now();
        $this->orientation = self::ORIENTATIONS[$type] ?? 'P';
        $this->contentWidth = $this->orientation === 'L' ? 273.0 : 186.0;
        $this->widths = $this->normalizeWidths($type, count($definition['columns']));
        $this->logoPath = $this->opaqueLogoPath($sourceLogo);
        $this->appName = (string) config('app.name');

        $this->DefOrientation = $this->orientation;
        $this->SetMargins(self::MARGIN_X, 14, self::MARGIN_X);
        $this->SetAutoPageBreak(true, self::BOTTOM_MARGIN);
        $this->AliasNbPages('{nb}');

        $this->AddFont('dejavu', '', 'DejaVuSans.ttf', true);
        $this->AddFont('dejavu', 'B', 'DejaVuSans-Bold.ttf', true);

        $this->AddPage();

        if ($rows === []) {
            $this->drawEmptyState();
        } else {
            $this->drawRows($rows);
        }

        return $this->Output('S');
    }

    public function Header(): void
    {
        if ($this->page === 0) {
            return;
        }

        $this->setHeaderBlock();
        $this->drawColumnHeaders();

        $this->SetY($this->headerEndY());
        $this->SetX(self::MARGIN_X);
    }

    public function Footer(): void
    {
        if ($this->page === 0) {
            return;
        }

        $this->SetY(-self::BOTTOM_MARGIN);
        $this->SetDrawColor(...self::LIGHT_GRAY);
        $this->SetLineWidth(0.2);
        $this->Line(self::MARGIN_X, $this->GetY(), self::MARGIN_X + $this->contentWidth, $this->GetY());

        $this->SetY(-(self::BOTTOM_MARGIN - 3));
        $this->SetFont('dejavu', '', 8);
        $this->SetTextColor(...self::FOOTER_GRAY);
        $this->Cell($this->contentWidth / 2, 5, 'Powered by '.$this->appName, 0, 0, 'L');
        $this->Cell($this->contentWidth / 2, 5, 'Page '.$this->PageNo().' of {nb}', 0, 0, 'R');
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function drawRows(array $rows): void
    {
        $i = 0;

        foreach ($rows as $row) {
            $cells = $this->prepareCells($row);
            $rowHeight = 0.0;

            foreach ($cells as $j => $text) {
                $height = $this->nbLines($this->widths[$j], $text) * self::LINE_HEIGHT + 1.2;
                $rowHeight = max($rowHeight, $height);
            }

            if ($this->GetY() + $rowHeight > $this->PageBreakTrigger) {
                $this->AddPage();
            }

            $y = $this->GetY();

            if ($i % 2 === 1) {
                $this->SetFillColor(...self::ZEBRA);
                $this->Rect(self::MARGIN_X, $y, $this->contentWidth, $rowHeight, 'F');
            }

            $this->SetFont('dejavu', '', 8);
            $this->SetTextColor(...self::BODY_TEXT);
            $x = self::MARGIN_X;

            foreach ($cells as $j => $text) {
                $this->SetXY($x, $y);
                $this->MultiCell($this->widths[$j], self::LINE_HEIGHT, $text, 0, 'L');
                $x += $this->widths[$j];
            }

            $this->SetDrawColor(...self::LIGHT_GRAY);
            $this->SetLineWidth(0.15);
            $this->Line(self::MARGIN_X, $y + $rowHeight, self::MARGIN_X + $this->contentWidth, $y + $rowHeight);

            $this->SetXY(self::MARGIN_X, $y + $rowHeight);
            $i++;
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<int, string>
     */
    private function prepareCells(array $row): array
    {
        $cells = [];

        foreach (array_keys($this->definition['columns']) as $key) {
            $value = $row[$key] ?? null;

            if ($value === null || $value === '') {
                $value = '—';
            }

            $cells[] = str_replace(["\r", "\n"], ['', ' '], (string) $value);
        }

        return $cells;
    }

    private function drawEmptyState(): void
    {
        $this->SetFont('dejavu', '', 8);
        $this->SetTextColor(...self::FOOTER_GRAY);
        $this->SetXY(self::MARGIN_X, $this->GetY() + 10);
        $this->Cell(0, 8, 'No records found.', 0, 1, 'C');
    }

    private function setHeaderBlock(): void
    {
        $y = $this->tMargin;
        $titleX = self::MARGIN_X;

        if ($this->logoPath !== null) {
            $logoWidth = $this->logoWidthMm();

            if ($logoWidth > 0) {
                $this->Image($this->logoPath, self::MARGIN_X, $y, $logoWidth, 13);
                $titleX = self::MARGIN_X + $logoWidth + 6;
            }
        }

        $this->SetFont('dejavu', 'B', 16);
        $this->SetTextColor(...self::NAVY);
        $this->SetXY($titleX, $y + 0.5);
        $this->Cell(0, 8, (string) $this->definition['title'], 0, 1);

        $this->SetFont('dejavu', '', 8.5);
        $this->SetTextColor(...self::GRAY);
        $this->SetXY($titleX, $y + 9);
        $this->Cell(0, 5, $this->appName.' · Generated '.$this->generatedAt->format('M d, Y H:i').' · '.number_format($this->rowCount).' records', 0, 1);

        $this->SetDrawColor(...self::NAVY);
        $this->SetLineWidth(0.6);
        $this->Line(self::MARGIN_X, $y + 16, self::MARGIN_X + $this->contentWidth, $y + 16);

        if ($this->page === 1) {
            $this->drawFilters($y + 18);
        }
    }

    private function drawFilters(float $y): void
    {
        $active = [];

        foreach ($this->filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $active[] = str_replace('_', ' ', $key).': '.$value;
            }
        }

        if ($active === []) {
            return;
        }

        $this->SetFont('dejavu', '', 8.5);
        $this->SetTextColor(...self::GRAY);
        $this->SetXY(self::MARGIN_X, $y);
        $this->Cell(0, 5, 'Filters: '.implode(' · ', $active), 0, 1);
    }

    private function drawColumnHeaders(): void
    {
        $y = $this->headerStartY();
        $x = self::MARGIN_X;

        $this->SetFont('dejavu', 'B', 8);
        $this->SetFillColor(...self::NAVY);
        $this->SetTextColor(255, 255, 255);

        $i = 0;

        foreach ($this->definition['columns'] as $label) {
            $this->SetXY($x, $y);
            $this->Cell($this->widths[$i], self::HEADER_ROW_HEIGHT, (string) $label, 1, 0, 'L', true);
            $x += $this->widths[$i];
            $i++;
        }
    }

    private function headerStartY(): float
    {
        return $this->hasFiltersOnFirstPage() ? $this->tMargin + 24.5 : $this->tMargin + 18.5;
    }

    private function headerEndY(): float
    {
        return $this->headerStartY() + self::HEADER_ROW_HEIGHT;
    }

    private function hasFiltersOnFirstPage(): bool
    {
        if ($this->page !== 1) {
            return false;
        }

        foreach ($this->filters as $value) {
            if ($value !== null && $value !== '') {
                return true;
            }
        }

        return false;
    }

    private function logoWidthMm(): float
    {
        $info = @getimagesize($this->logoPath);

        if ($info === false || ($info[1] ?? 0) === 0) {
            return 0;
        }

        return min(13 * ($info[0] / $info[1]), 50);
    }

    /**
     * @return array<int, float>
     */
    private function normalizeWidths(string $type, int $columnCount): array
    {
        $widths = self::WIDTHS[$type] ?? [];

        if (count($widths) !== $columnCount) {
            $widths = array_fill(0, $columnCount, 1);
        }

        $total = array_sum($widths);

        if ($total <= 0) {
            $widths = array_fill(0, $columnCount, 1);
            $total = $columnCount;
        }

        return array_map(fn (float $w): float => round($w * $this->contentWidth / $total, 2), $widths);
    }

    /**
     * Number of lines a cell's text needs at the current font and given width,
     * mirroring tFPDF's MultiCell word-wrapping algorithm exactly.
     */
    private function nbLines(float $width, string $text): int
    {
        $wmax = $width - 2 * $this->cMargin;
        $s = str_replace("\r", '', $text);
        $nb = mb_strlen($s, 'UTF-8');

        while ($nb > 0 && mb_substr($s, $nb - 1, 1, 'UTF-8') === "\n") {
            $nb--;
        }

        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0.0;
        $nl = 1;

        while ($i < $nb) {
            $c = mb_substr($s, $i, 1, 'UTF-8');

            if ($c === "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0.0;
                $nl++;

                continue;
            }

            if ($c === ' ') {
                $sep = $i;
            }

            $l += $this->GetStringWidth($c);

            if ($l > $wmax) {
                if ($sep === -1) {
                    if ($i === $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }

                $sep = -1;
                $j = $i;
                $l = 0.0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $nl;
    }

    private function opaqueLogoPath(?string $source): ?string
    {
        if ($source === null) {
            return null;
        }

        $dir = storage_path('app/pdf');
        $target = $dir.DIRECTORY_SEPARATOR.'scripcom_logo_opaque.png';

        try {
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $src = @imagecreatefrompng($source);

            if ($src === false) {
                return null;
            }

            $width = imagesx($src);
            $height = imagesy($src);
            $dst = imagecreatetruecolor($width, $height);
            imagealphablending($dst, true);
            imagesavealpha($dst, false);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopy($dst, $src, 0, 0, 0, 0, $width, $height);

            if (imagepng($dst, $target) === false) {
                return null;
            }

            imagedestroy($src);
            imagedestroy($dst);

            return $target;
        } catch (\Throwable $e) {
            Log::warning('Failed to flatten report logo: '.$e->getMessage());

            return null;
        }
    }
}
