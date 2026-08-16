<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Http\Controllers\Traits\HasOrganizationScoping;
use App\Services\ReportPdfService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Excel as ExcelFormat;

class ReportController extends Controller
{
    use HasOrganizationScoping;

    public function index(): View
    {
        $definitions = app(ReportService::class)->definitions();

        return view('reports.index', compact('definitions'));
    }

    public function show(Request $request, string $type): View
    {
        $reports = app(ReportService::class);
        $definition = $reports->definition($type) ?? abort(404);
        $filters = $this->filters($request, $definition);
        $rows = $reports->data($type, $filters);
        $options = $reports->options($type);

        $page = Paginator::resolveCurrentPage();
        $perPage = 25;
        $paginated = new LengthAwarePaginator(
            array_slice($rows, ($page - 1) * $perPage, $perPage),
            count($rows),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('reports.show', compact('definition', 'type', 'filters', 'options', 'paginated', 'rows'));
    }

    public function export(Request $request, string $type, string $format)
    {
        abort_unless($request->user()->can('export-reports'), 403);

        if (! in_array($format, ['xlsx', 'csv', 'pdf'], true)) {
            abort(404);
        }

        $reports = app(ReportService::class);
        $definition = $reports->definition($type) ?? abort(404);
        $filters = $this->filters($request, $definition);
        $rows = $reports->data($type, $filters);

        $filename = Str::slug($definition['title']).'-'.now()->format('Y-m-d-His').'.'.$format;

        if ($format === 'pdf') {
            $pdf = app(ReportPdfService::class)->render($type, $definition, $filters, $rows, $this->logoPath());

            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
                ->header('Content-Length', (string) strlen($pdf))
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache');
        }

        $headings = array_values($definition['columns']);
        $export = new DataExport(
            $headings,
            array_map(fn (array $row) => array_values($row), $rows),
            $format === 'xlsx' ? $this->logoPath() : null
        );

        $response = $format === 'csv'
            ? $export->download($filename, ExcelFormat::CSV, ['Content-Type' => 'text/csv'])
            : $export->download($filename, ExcelFormat::XLSX);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<string, mixed>
     */
    private function filters(Request $request, array $definition): array
    {
        $filters = [];

        foreach ($definition['filters'] as $key) {
            $filters[$key] = $request->input($key);
        }

        return $filters;
    }

    private function logoPath(): ?string
    {
        $path = public_path('scripcom_logo.png');

        if (! is_file($path) || ! is_readable($path)) {
            return null;
        }

        $info = @getimagesize($path);

        if ($info === false || ($info[2] ?? null) !== IMAGETYPE_PNG) {
            Log::warning("Report logo is not a valid PNG image: {$path}");

            return null;
        }

        return $path;
    }
}
