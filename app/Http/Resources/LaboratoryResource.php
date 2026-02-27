<?php

namespace App\Http\Resources;

use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryResource extends JsonResource
{


    public static $wrap = null;
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'ext' => $this->ext,
            'birthdate' => $this->birthdate,
            'contact_number' => $this->contact_number,
            'age' => $this->age,
            'gender' => $this->gender,
            'is_not_tagum' => $this->is_not_tagum,
            'street' => $this->street,
            'purok' => $this->purok,
            'barangay' => $this->barangay,
            'city' => $this->city,
            'province' => $this->province,
            'category' => $this->category,
            'is_pwd' => $this->is_pwd,
            'is_solo' => $this->is_solo,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'transaction' => TransactionResource::collection(
                $this->whenLoaded('transaction')
            ),
        ];
    }
}
