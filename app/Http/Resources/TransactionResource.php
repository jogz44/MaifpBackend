<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'transaction_number' => $this->transaction_number,
            'transaction_type' => $this->transaction_type,
            'status' => $this->status,
            'transaction_date' => $this->transaction_date,
            'transaction_mode' => $this->transaction_mode,
            'purpose' => $this->purpose,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'representative_id' => $this->representative_id,

            'consultation' => new ConsultationResource($this->whenLoaded('consultation')),
            'vital' => new VitalResource($this->whenLoaded('vital')),
            // 'laboratory' => $this->whenLoaded('laboratory'),
        ];
    }
}
