<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'height' => $this->height,
            'weight' => $this->weight,
            'bmi' => $this->bmi,
            'pulse_rate' => $this->pulse_rate,
            'temperature' => $this->temperature,
            'sp02' => $this->sp02,
            'heart_rate' => $this->heart_rate,
            'blood_pressure' => $this->blood_pressure,
            'respiratory_rate' => $this->respiratory_rate,
            'medicine' => $this->medicine,
            'LMP' => $this->LMP



        ];
    }
}
