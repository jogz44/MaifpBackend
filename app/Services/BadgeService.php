<?php

namespace App\Services;

use App\Models\vw_patient_assessment_maifip;
use App\Models\vw_patient_laboratory;
use App\Models\vw_patient_medication;
use App\Models\vw_patient_consultation;
use App\Models\vw_transaction_complete;
use App\Http\Requests\PatientRequestAll;
use App\Models\vw_patient_billing;
use App\Models\vw_patient_assessment_philhealth;

use App\Models\vw_patient_consultation_return;
class BadgeService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

public   function getBadgeCounts()
    {
        return [
            'totalAssessedCount'   => vw_patient_assessment_maifip::distinct('patient_id')->count('patient_id'),
            'totalQualifiedCount'  => vw_patient_consultation::distinct('patient_id')->count('patient_id'),
            'totalLaboratoryCount' => vw_patient_laboratory::distinct('patient_id')->count('patient_id'),
            'totalMedicationCount' => vw_patient_medication::distinct('patient_id')->count('patient_id'),
            'totalReturnedCount'   => vw_patient_consultation_return::distinct('patient_id')->count('patient_id'),
            'totalBillingCount'    => vw_patient_billing::distinct('patient_id')->count('patient_id'),
            'totalGLCount'         => vw_transaction_complete::distinct('patient_id')->count('patient_id'),
            'totalphilhealth'      => vw_patient_assessment_philhealth::distinct('patient_id')->count('patient_id'),
        ];
    }
}
