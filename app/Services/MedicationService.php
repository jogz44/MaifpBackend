<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class MedicationService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }


    public function medication()
    {

        $patientMed = Patient::with([
            'transaction.consultation',
            'transaction.vital',
            'transaction.laboratories',
            'transaction.medication'
        ])
            ->whereHas('transaction', function ($transaction) {

                $transaction->whereIn('status', ['Qualified', 'Pending'])

                    ->where(function ($q2) {
                        $q2->whereIn('transaction_type', ['Laboratory','Consultation','Medication'])
                            ->orWhereHas('consultation', function ($consultation) {
                                $consultation->whereIn('status', ['Processing','Returned','Medication']);
                            });
                    })

                    // ✅ FIX 1: medication condition moved here
                    ->whereDoesntHave('medication', function ($med) {
                        $med->where('status', 'Done');
                    })

                    // ✅ FIX 2: laboratory condition still correct
                    ->whereDoesntHave('laboratories', function ($lab) {
                        $lab->whereIn('status', ['Done', 'Pending']);
                    });
            })
            ->get();

        // $total_patient_med = $patientMed->count();

        return $patientMed;
    }
}
