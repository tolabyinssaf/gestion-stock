<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Sanitize string against Excel Formula Injection (CSV/formula injection).
     */
    public static function sanitizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;

        // If cell starts with formula trigger characters, prefix with a single quote
        if (preg_match('/^[=+\-@\t\r]/', $str)) {
            $str = "'" . $str;
        }

        return $str;
    }

    /**
     * Stream a professional CSV/Excel compatible file with UTF-8 BOM.
     * Excel natively opens UTF-8 CSV with semicolon delimiter perfectly.
     *
     * @param string $filename
     * @param array<string> $headers
     * @param iterable $rows Each row is an array of cell values
     * @param array<string, string> $metadata Title, generated date, filters
     */
    public function streamExcelCsv(
        string $filename,
        array $headers,
        iterable $rows,
        array $metadata = []
    ): StreamedResponse {
        $response = new StreamedResponse(function () use ($headers, $rows, $metadata) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel to open accents properly (é, è, à, etc.)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Metadata / Title block
            if (!empty($metadata['title'])) {
                fputcsv($handle, ['IT MANAGER PRO — ' . $metadata['title']], ';');
                fputcsv($handle, ['Date de génération : ' . now()->format('d/m/Y à H:i:s')], ';');
                if (!empty($metadata['filters'])) {
                    fputcsv($handle, ['Filtres appliqués : ' . $metadata['filters']], ';');
                }
                fputcsv($handle, [], ';'); // blank line
            }

            // Headers
            fputcsv($handle, $headers, ';');

            // Rows
            foreach ($rows as $row) {
                $sanitizedRow = array_map([self::class, 'sanitizeCell'], $row);
                fputcsv($handle, $sanitizedRow, ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0, no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }

    /**
     * Generate HTML view formatted for clean print/PDF view.
     * Compatible with browser native print/save as PDF (landscape or portrait).
     */
    public function renderPdfHtml(
        string $title,
        array $headers,
        iterable $rows,
        array $metadata = [],
        string $orientation = 'portrait'
    ): string {
        return view('it.reports.pdf-template', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'metadata' => $metadata,
            'orientation' => $orientation,
            'generatedAt' => now()->format('d/m/Y à H:i'),
        ])->render();
    }
}
