<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\vw_patient_assessment_maifip;
use App\Models\vw_patient_laboratory;
use App\Models\vw_patient_medication;
use App\Models\vw_patient_consultation;
use App\Models\vw_transaction_complete;
use App\Http\Requests\PatientRequestAll;
use App\Models\vw_patient_billing;
use App\Models\vw_patient_assessment_philhealth;

use App\Models\vw_patient_consultation_return;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class BadgeUpdated implements ShouldBroadcastNow // ← missing this!
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [new Channel('badge-channel')];
    }

    public function broadcastAs(): string
    {
        return 'badge.updated';
    }



}
