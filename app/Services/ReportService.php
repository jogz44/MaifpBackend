<?php

namespace App\Services;

use App\Models\Transaction;
use PhpOffice\PhpSpreadsheet\IOFactory;
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



      // doh report
    public function exportExcelReport($validated)
    {


        $ids = $validated['ids'];
        $templatePath = storage_path('app/template/publicationVacant.xlsm');

        $reader = IOFactory::createReader('Xlsx');
        $reader->setIncludeCharts(true);

        $spreadsheet = $reader->load($templatePath);

        // VERY IMPORTANT → preserve macros
        if ($spreadsheet->hasMacros()) {
            $spreadsheet->setMacrosCode($spreadsheet->getMacrosCode());
        }

        $sheet = $spreadsheet->getActiveSheet();

        $jobs = DB::table('vwplantillaStructure')
            ->whereIn('ID', $ids)
            ->get();

        $competency = DB::table('vwplantillalevel')
            ->whereIn('ID', $ids)
            ->select('ID', 'SG', 'Level')
            ->get()
            ->keyBy('ID');


        // Insert extra rows if more than 5 jobs (template has rows 18–22 pre-filled)
        $extraRows = count($jobs) - 5;
        if ($extraRows > 0) {
            $sheet->insertNewRowBefore(23, $extraRows);
        }

        $row = 18;
        $no = 1;

        foreach ($jobs as $job) {
            $qs = DB::table('yDesignationQS2')
                ->where('PositionID', $job->PositionID)
                ->first();

            $salary = DB::table('tblSalarySchedule')
                ->where('Grade', $job->SG)
                ->where('Steps', 1)  // double-check your column name: 'Steps' or 'Step'
                ->first();
            $levelData = $competency[$job->ID] ?? null;

            $sheet->setCellValue("A{$row}", $no);
            $sheet->setCellValue("B{$row}", $job->position ?? '');
            $sheet->setCellValue("C{$row}", $job->ItemNo ?? '');
            $sheet->setCellValue("D{$row}", $job->SG ?? '');
            $sheet->setCellValue("E{$row}", $salary->Salary ?? '');
            $sheet->setCellValue("F{$row}", $qs->Education ?? '');
            $sheet->setCellValue("G{$row}", $qs->Training ?? '');
            $sheet->setCellValue("H{$row}", $qs->Experience ?? '');
            $sheet->setCellValue("I{$row}", $qs->Eligibility ?? '');
            // $sheet->setCellValue("J{$row}", $competency->competency()?? '');
            $sheet->setCellValue(
                "J{$row}",
                $levelData
                    ? $this->competency($levelData->SG, $levelData->Level)
                    : ''
            );

            $sheet->setCellValue(
                "K{$row}",
                implode(' - ', array_filter([
                    $job->office ?? '',
                    $job->office2 ?? '',
                    $job->group ?? '',
                    $job->division ?? '',
                    $job->section ?? '',
                ]))
            );
            // $sheet->setCellValue("K{$row}", $job-> ?? '');
            // $sheet->setCellValue("K{$row}", $job->office ?? '');

            $row++;
            $no++;
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->setIncludeCharts(true);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
            'Content-Disposition' => 'attachment; filename="Request for Publication of Vacant Positions.xlsm"',
        ]);
    }


}
