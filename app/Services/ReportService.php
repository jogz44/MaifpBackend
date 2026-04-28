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


    //  // export excel doh report 
    // public function exportExcelReport($validated)
    // {
    //     try {


    //         $fromDate = Carbon::parse($validated['fromDate'])->format('Y-m-d');
    //         $toDate   = isset($validated['toDate'])
    //             ? Carbon::parse($validated['toDate'])->format('Y-m-d')
    //             : null;
    //         $templatePath = storage_path('app/template/ANNEX-B-REPORT.xlsx');

    //         // Log::info('exportExcelReport started', [
    //         //     'fromDate'       => $fromDate,
    //         //     'toDate'         => $toDate,
    //         //     'templatePath'   => $templatePath,
    //         //     'templateExists' => file_exists($templatePath),
    //         // ]);

    //         if (!file_exists($templatePath)) {
    //             // Log::error('Template file not found', ['path' => $templatePath]);
    //             return response()->json(['message' => 'Template file not found.'], 500);
    //         }

    //         $reader = IOFactory::createReader('Xlsx');
    //         $reader->setIncludeCharts(true);

    //         $spreadsheet = $reader->load($templatePath);
    //         // Log::info('Template loaded successfully');

    //         if ($spreadsheet->hasMacros()) {
    //             $spreadsheet->setMacrosCode($spreadsheet->getMacrosCode());
    //         }

    //         // $sheet = $spreadsheet->getActiveSheet();
    //         $sheet = $spreadsheet->getSheetByName('OUT-PATIENT'); // change if there is specific sheet need to fill out data

    //         $transactions = Transaction::select('id', 'transaction_type', 'transaction_date', 'patient_id')
    //             ->when(
    //                 $toDate,
    //                 // ✅ if toDate is provided — get range
    //                 fn($q) => $q->whereBetween('transaction_date', [$fromDate, $toDate]),
    //                 // ✅ if toDate is null — get only fromDate
    //                 fn($q) => $q->whereDate('transaction_date', $fromDate)
    //             )
    //             ->with([
    //                 'patient'          => fn($q) => $q->select('id', 'firstname', 'lastname', 'middlename'),
    //                 'assistance'       => fn($q) => $q->select(
    //                     'id',
    //                     'gl_lgu',
    //                     'transaction_id',
    //                     'radiology_total',
    //                     'examination_total',
    //                     'mammogram_total',
    //                     'ultrasound_total',
    //                     'consultation_amount',
    //                     'medication_total',
    //                     'total_billing',
    //                     'discount',
    //                     'final_billing'
    //                 ),
    //                 'assistance.funds' => fn($q) => $q->select('id', 'assistance_id', 'fund_source', 'fund_amount')
    //                     ->where('fund_source', 'MAIFIP-LGU'),
    //             ])
    //             ->whereHas('patient')
    //             ->whereHas('assistance', function ($q) {
    //                 $q->whereNotNull('gl_lgu')
    //                     ->where('gl_lgu', '!=', '');
    //             })
    //             ->get();

    //         // Log::info('Transactions fetched', ['count' => $transactions->count()]);

    //         $extraRows = count($transactions) - 5;
    //         if ($extraRows > 0) {
    //             $sheet->insertNewRowBefore(240, $extraRows);
    //             // Log::info('Extra rows inserted', ['extraRows' => $extraRows]);
    //         }

    //                 $totalPatient = 0;
    //                 $totalNoDeduction = 0;
    //                 $totalMaifipAmount = 0;


    //         $row = 39;
    //         $no  = 1;

    //         $fromCarbon = Carbon::parse($fromDate);
    //         $toCarbon   = $toDate ? Carbon::parse($toDate) : null;

    //         if ($toCarbon && $fromCarbon->format('m') !== $toCarbon->format('m')) {
    //             // different months → "April - May 2026"
    //             $monthLabel = $fromCarbon->format('F') . ' - ' . $toCarbon->format('F Y');
    //         } else {
    //             // same month or no toDate → "April 2026"
    //             $monthLabel = $fromCarbon->format('F Y');
    //         }

    //         // header
    //         $sheet->setCellValue("C9", "For the Month of " . $monthLabel);


    //         // body
    //         foreach ($transactions as $transaction) {
    //             $patient    = $transaction->patient;
    //             $assistance = $transaction->assistance;
    //             $fund       = $assistance->funds->first();

    //             $fullName = '';
    //             if ($patient) {
    //                 $fullName = strtoupper($patient->lastname) . ', '
    //                     . $patient->firstname . ' '
    //                     . ($patient->middlename ?? '');
    //             }

    //             // Log::info("Writing row {$row}", [
    //             //     'no'            => $no,
    //             //     'fullName'      => trim($fullName),
    //             //     'gl_lgu'        => $assistance->gl_lgu        ?? null,
    //             //     'final_billing' => $assistance->final_billing  ?? null,
    //             //     'discount'      => $assistance->discount       ?? null,
    //             // ]);

    //                $types = [];

    //             $laboratoryTotal = optional($assistance)->radiology_total
    //                 + optional($assistance)->examination_total
    //                 + optional($assistance)->mammogram_total
    //                 + optional($assistance)->ultrasound_total;

    //             if (optional($assistance)->consultation_amount > 0) {
    //                 $types[] = 'CONSULTATION';
    //             }

    //             if ($laboratoryTotal > 0) {
    //                 $types[] = 'LABORATORY';
    //             }

    //             if (optional($assistance)->medication_total > 0) {
    //                 $types[] = 'MEDICINE';
    //             }

    //             $sheet->setCellValue("B{$row}", $no);
    //             $sheet->setCellValue("C{$row}", $this->upper($fullName));
    //             $sheet->setCellValue("D{$row}", $assistance->gl_lgu        ?? '');
    //             $sheet->setCellValue("E{$row}", implode('/', $types)); // e.g. "CONSULTATION/LABORATORY/MEDICINE"     
    //             $sheet->setCellValue("F{$row}", $assistance->final_billing  ?? '');
    //             // $sheet->setCellValue("G{$row}", $assistance->discount       ?? '');
    //             $sheet->setCellValue("P{$row}", $fund->fund_amount          ?? '');

    //             $row++;
    //             $no++;
    //         }
    //                 // footer
    //         $sheet->setCellValue("F240", $totalPatient);
    //         $sheet->setCellValue("F241", $totalNoDeduction);
    //         $sheet->setCellValue("P241", $totalMaifipAmount);

    //         // summary
    //         $sheet->setCellValue("D247", $totalPatient);
    //         $sheet->setCellValue("E247", $totalNoDeduction);
    //         $sheet->setCellValue("F247", $totalMaifipAmount);

    //         $sheet->setCellValue("D248", $totalPatient);
    //         $sheet->setCellValue("E248", $totalNoDeduction);
    //         $sheet->setCellValue("F248", $totalMaifipAmount);

    //         $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    //         $writer->setIncludeCharts(true);

    //         $tempFile = tempnam(sys_get_temp_dir(), 'annex_b_') . '.xlsx';
    //         $writer->save($tempFile);

    //         // Log::info('Excel file saved to temp', [
    //         //     'tempFile' => $tempFile,
    //         //     'fileSize' => filesize($tempFile),
    //         // ]);

    //         return response()->download($tempFile, 'ANNEX-B-REPORT.xlsx', [
    //             'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //             'Content-Disposition' => 'attachment; filename="ANNEX-B-REPORT.xlsx"',
    //             'Cache-Control'       => 'max-age=0',
    //             'Pragma'              => 'public',
    //         ])->deleteFileAfterSend(true);
    //     } catch (\Throwable $e) {
    //         // Log::error('exportExcelReport failed', [
    //         //     'message' => $e->getMessage(),
    //         //     'file'    => $e->getFile(),
    //         //     'line'    => $e->getLine(),
    //         //     'trace'   => $e->getTraceAsString(),
    //         // ]);

    //         return response()->json([
    //             'message' => 'Failed to generate Excel report.',
    //             'error'   => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function exportExcelReport($validated)
    {
        try {
            $fromDate = Carbon::parse($validated['fromDate'])->format('Y-m-d');
            $toDate   = isset($validated['toDate'])
                ? Carbon::parse($validated['toDate'])->format('Y-m-d')
                : null;
            $templatePath = storage_path('app/template/ANNEX-B-REPORT.xlsx');

            if (!file_exists($templatePath)) {
                return response()->json(['message' => 'Template file not found.'], 500);
            }

            $reader = IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);
            $spreadsheet = $reader->load($templatePath);

            if ($spreadsheet->hasMacros()) {
                $spreadsheet->setMacrosCode($spreadsheet->getMacrosCode());
            }

            $sheet = $spreadsheet->getSheetByName('OUT-PATIENT');

            $transactions = Transaction::select('id', 'transaction_type', 'transaction_date', 'patient_id')
                ->when(
                    $toDate,
                    fn($q) => $q->whereBetween('transaction_date', [$fromDate, $toDate]),
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
                    $q->whereNotNull('gl_lgu')->where('gl_lgu', '!=', '');
                })
                ->get();

            // ============================================================
            // ✅ These are the ORIGINAL row numbers in the blank template
            // ============================================================
            $originalDataStartRow = 39;
            $originalFooterRow1   = 240; // total patient row
            $originalFooterRow2   = 241; // total billing / maifip row
            $originalSummaryRow1  = 247;
            $originalSummaryRow2  = 248;
            $defaultRowsInTemplate = 5; // how many data rows the template has by default

            // ✅ Calculate how many extra rows need to be inserted
            $extraRows = count($transactions) - $defaultRowsInTemplate;

            if ($extraRows > 0) {
                $sheet->insertNewRowBefore($originalFooterRow1, $extraRows);
            }

            // ✅ Shift footer/summary rows down by the same amount inserted
            $offset      = max(0, $extraRows);
            $footerRow1  = $originalFooterRow1  + $offset;
            $footerRow2  = $originalFooterRow2  + $offset;
            $summaryRow1 = $originalSummaryRow1 + $offset;
            $summaryRow2 = $originalSummaryRow2 + $offset;

            // ============================================================
            // Header
            // ============================================================
            $fromCarbon = Carbon::parse($fromDate);
            $toCarbon   = $toDate ? Carbon::parse($toDate) : null;

            if ($toCarbon && $fromCarbon->format('m') !== $toCarbon->format('m')) {
                $monthLabel = $fromCarbon->format('F') . ' - ' . $toCarbon->format('F Y');
            } else {
                $monthLabel = $fromCarbon->format('F Y');
            }

            $sheet->setCellValue("C9", "For the Month of " . $monthLabel);

            // ============================================================
            // ✅ Initialize totals BEFORE the loop
            // ============================================================
            $totalPatient      = 0;
            $totalNoDeduction  = 0;
            $totalMaifipAmount = 0;

            $row = $originalDataStartRow;
            $no  = 1;

            // ============================================================
            // Body
            // ============================================================
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

                $types = [];

                $laboratoryTotal = optional($assistance)->radiology_total
                    + optional($assistance)->examination_total
                    + optional($assistance)->mammogram_total
                    + optional($assistance)->ultrasound_total;

                if (optional($assistance)->consultation_amount > 0) $types[] = 'CONSULTATION';
                if ($laboratoryTotal > 0)                           $types[] = 'LABORATORY';
                if (optional($assistance)->medication_total > 0)   $types[] = 'MEDICINE';

                $finalBilling = $assistance->final_billing ?? 0;
                $fundAmount   = $fund->fund_amount         ?? 0;

                $sheet->setCellValue("B{$row}", $no);
                $sheet->setCellValue("C{$row}", $this->upper($fullName));
                $sheet->setCellValue("D{$row}", $assistance->gl_lgu ?? '');
                $sheet->setCellValue("E{$row}", implode('/', $types));
                $sheet->setCellValue("F{$row}", $finalBilling);
                $sheet->setCellValue("P{$row}", $fundAmount);

                // ✅ Accumulate totals inside the loop
                $totalPatient++;
                $totalNoDeduction  += $finalBilling;
                $totalMaifipAmount += $fundAmount;

                $row++;
                $no++;
            }

            // ============================================================
            // ✅ Footer — uses dynamic rows, always correct no matter the count
            // ============================================================
            $sheet->setCellValue("F{$footerRow1}", $totalPatient);
            $sheet->setCellValue("F{$footerRow2}", $totalNoDeduction);
            $sheet->setCellValue("P{$footerRow2}", $totalMaifipAmount);

            // ✅ Summary — uses dynamic rows
            $sheet->setCellValue("D{$summaryRow1}", $totalPatient);
            $sheet->setCellValue("E{$summaryRow1}", $totalNoDeduction);
            $sheet->setCellValue("F{$summaryRow1}", $totalMaifipAmount);

            $sheet->setCellValue("D{$summaryRow2}", $totalPatient);
            $sheet->setCellValue("E{$summaryRow2}", $totalNoDeduction);
            $sheet->setCellValue("F{$summaryRow2}", $totalMaifipAmount);

            // ============================================================
            // Write & Download
            // ============================================================
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);

            $tempFile = tempnam(sys_get_temp_dir(), 'annex_b_') . '.xlsx';
            $writer->save($tempFile);

            return response()->download($tempFile, 'ANNEX-B-REPORT.xlsx', [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="ANNEX-B-REPORT.xlsx"',
                'Cache-Control'       => 'max-age=0',
                'Pragma'              => 'public',
            ])->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
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
