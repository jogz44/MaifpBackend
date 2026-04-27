<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // only patient will fetch have gl_lgu
    public function medicalAssistanceReportMaip()
    {
        $transactions = Transaction::select('id', 'transaction_type', 'transaction_date', 'patient_id')
            ->with([
                'patient'        => fn($q) => $q->select('id', 'firstname', 'lastname', 'middlename'),
                'assistance'     => fn($q) => $q->select(
                    'id',
                    'gl_lgu',
                    'transaction_id',
                    'radiology_total',
                    'examination_total',
                    'mammogram_total',
                    'ultrasound_total',
                    'consultation_amount',
                    'medication_total',
                    'total_billing',
                    'discount',
                    'final_billing'
                ),
                'assistance.funds' => fn($q) => $q->select('id', 'assistance_id', 'fund_source', 'fund_amount'),
            ])
            ->whereHas('patient')
            ->whereHas('assistance', function ($q) {
                $q->whereNotNull('gl_lgu')   // must have gl_lgu
                    ->where('gl_lgu', '!=', ''); //must not be empty string
            })
            ->get();

        return $transactions;
    }


     // export excel doh report 
    public function exportExcelReport($validated)
    {
        try {


            $fromDate = Carbon::parse($validated['fromDate'])->format('Y-m-d');
            $toDate   = isset($validated['toDate'])
                ? Carbon::parse($validated['toDate'])->format('Y-m-d')
                : null;
            $templatePath = storage_path('app/template/ANNEX-B-REPORT.xlsx');

            // Log::info('exportExcelReport started', [
            //     'fromDate'       => $fromDate,
            //     'toDate'         => $toDate,
            //     'templatePath'   => $templatePath,
            //     'templateExists' => file_exists($templatePath),
            // ]);

            if (!file_exists($templatePath)) {
                Log::error('Template file not found', ['path' => $templatePath]);
                return response()->json(['message' => 'Template file not found.'], 500);
            }

            $reader = IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);

            $spreadsheet = $reader->load($templatePath);
            // Log::info('Template loaded successfully');

            if ($spreadsheet->hasMacros()) {
                $spreadsheet->setMacrosCode($spreadsheet->getMacrosCode());
            }

            $sheet = $spreadsheet->getActiveSheet();

            $transactions = Transaction::select('id', 'transaction_type', 'transaction_date', 'patient_id')
                ->when(
                    $toDate,
                    // ✅ if toDate is provided — get range
                    fn($q) => $q->whereBetween('transaction_date', [$fromDate, $toDate]),
                    // ✅ if toDate is null — get only fromDate
                    fn($q) => $q->whereDate('transaction_date', $fromDate)
                )
                ->with([
                    'patient'          => fn($q) => $q->select('id', 'firstname', 'lastname', 'middlename'),
                    'assistance'       => fn($q) => $q->select(
                        'id',
                        'gl_lgu',
                        'transaction_id',
                        'radiology_total',
                        'examination_total',
                        'mammogram_total',
                        'ultrasound_total',
                        'consultation_amount',
                        'medication_total',
                        'total_billing',
                        'discount',
                        'final_billing'
                    ),
                    'assistance.funds' => fn($q) => $q->select('id', 'assistance_id', 'fund_source', 'fund_amount')
                        ->where('fund_source', 'MAIFIP-LGU'),
                ])
                ->whereHas('patient')
                ->whereHas('assistance', function ($q) {
                    $q->whereNotNull('gl_lgu')
                        ->where('gl_lgu', '!=', '');
                })
                ->get();

            // Log::info('Transactions fetched', ['count' => $transactions->count()]);

            $extraRows = count($transactions) - 5;
            if ($extraRows > 0) {
                $sheet->insertNewRowBefore(262, $extraRows);
                // Log::info('Extra rows inserted', ['extraRows' => $extraRows]);
            }

            $row = 257;
            $no  = 1;

            $fromCarbon = Carbon::parse($fromDate);
            $toCarbon   = $toDate ? Carbon::parse($toDate) : null;

            if ($toCarbon && $fromCarbon->format('m') !== $toCarbon->format('m')) {
                // different months → "April - May 2026"
                $monthLabel = $fromCarbon->format('F') . ' - ' . $toCarbon->format('F Y');
            } else {
                // same month or no toDate → "April 2026"
                $monthLabel = $fromCarbon->format('F Y');
            }


            $sheet->setCellValue("C9", "For the Month of " . $monthLabel);


            foreach ($transactions as $transaction) {
                $patient    = $transaction->patient;
                $assistance = $transaction->assistance;
                $fund       = $assistance->funds->first();

                $fullName = '';
                if ($patient) {
                    $fullName = strtoupper($patient->lastname) . ', '
                        . $patient->firstname . ' '
                        . ($patient->middlename ?? '');
                }

                // Log::info("Writing row {$row}", [
                //     'no'            => $no,
                //     'fullName'      => trim($fullName),
                //     'gl_lgu'        => $assistance->gl_lgu        ?? null,
                //     'final_billing' => $assistance->final_billing  ?? null,
                //     'discount'      => $assistance->discount       ?? null,
                // ]);

                $sheet->setCellValue("B{$row}", $no);
                $sheet->setCellValue("C{$row}", $this->upper($fullName));
                $sheet->setCellValue("D{$row}", $assistance->gl_lgu        ?? '');
                $sheet->setCellValue("F{$row}", $assistance->final_billing  ?? '');
                $sheet->setCellValue("G{$row}", $assistance->discount       ?? '');
                $sheet->setCellValue("P{$row}", $fund->fund_amount          ?? '');

                $row++;
                $no++;
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);

            $tempFile = tempnam(sys_get_temp_dir(), 'annex_b_') . '.xlsx';
            $writer->save($tempFile);

            // Log::info('Excel file saved to temp', [
            //     'tempFile' => $tempFile,
            //     'fileSize' => filesize($tempFile),
            // ]);

            return response()->download($tempFile, 'ANNEX-B-REPORT.xlsx', [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="ANNEX-B-REPORT.xlsx"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public',
            ])->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            // Log::error('exportExcelReport failed', [
            //     'message' => $e->getMessage(),
            //     'file'    => $e->getFile(),
            //     'line'    => $e->getLine(),
            //     'trace'   => $e->getTraceAsString(),
            // ]);

            return response()->json([
                'message' => 'Failed to generate Excel report.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    private function upper($value)
    {
        return strtoupper(trim($value ?? ''));
    }
}
