<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalAssistanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $assistance = $this->whenLoaded('assistance');
        $patient    = $this->whenLoaded('patient');

        return [
            'transaction_id'               => $this->id,
            'transaction_type' => $this->transaction_type,
            'transaction_date' => $this->transaction_date,

            // ✅ Use optional() to avoid null error
            // 'patient' => $this->when($patient, [
                'patient_id'         => optional($patient)->id,
                'firstname'  => optional($patient)->firstname,
                'middlename' => optional($patient)->middlename,
                'lastname'   => optional($patient)->lastname,
                'full_name'  => trim(optional($patient)->firstname . ' ' . optional($patient)->middlename . ' ' . optional($patient)->lastname),
            // ]),

            'assistance' => $this->when($assistance, function () use ($assistance) {

                // ✅ Build type_of_medical dynamically based on amount
                $types = [];

                $laboratoryTotal = optional($assistance)->radiology_total
                    + optional($assistance)->examination_total
                    + optional($assistance)->mammogram_total
                    + optional($assistance)->ultrasound_total;

                if (optional($assistance)->consultation_amount > 0) {
                    $types[] = 'CONSULTATION';
                }

                if ($laboratoryTotal > 0) {
                    $types[] = 'LABORATORY';
                }

                if (optional($assistance)->medication_total > 0) {
                    $types[] = 'MEDICINE';
                }

                return [
                    'assistance_id'                  => optional($assistance)->id,
                    'gl_lgu'              => optional($assistance)->gl_lgu,
                    'type_of_medical'     => implode('/', $types), // e.g. "CONSULTATION/LABORATORY/MEDICINE"
                    'radiology_total'     => number_format(optional($assistance)->radiology_total, 2),
                    'examination_total'   => number_format(optional($assistance)->examination_total, 2),
                    'mammogram_total'     => number_format(optional($assistance)->mammogram_total, 2),
                    'ultrasound_total'    => number_format(optional($assistance)->ultrasound_total, 2),
                    'consultation_amount' => number_format(optional($assistance)->consultation_amount, 2),
                    'medication_total'    => number_format(optional($assistance)->medication_total, 2),
                    'total_billing'       => number_format(optional($assistance)->total_billing, 2),
                    'discount'            => number_format(optional($assistance)->discount, 2),
                    'final_billing'       => number_format(optional($assistance)->final_billing, 2),

                    'funds' => optional($assistance)->funds?->map(fn($fund) => [
                        'fund_id'          => $fund->id,
                        'fund_source' => $fund->fund_source,
                        'fund_amount' => number_format($fund->fund_amount, 2),
                    ]),
                ];
            }),
        ];
    }
}
