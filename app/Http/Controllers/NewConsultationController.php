<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewConsultationRequest;
use App\Models\New_Consultation;
use Illuminate\Http\Request;

class NewConsultationController extends Controller
{
    //

    public function index(){

        $NewConsultation = New_Consultation::all();

        return response()->json([
            'message' => 'successfully',
            'consultation' => $NewConsultation
        ]);
    }

    public function show($id)
    {

        $NewConsultation = New_Consultation::findOrFail($id);

        return response()->json([
            'message' => 'successfully',
            'consultation' => $NewConsultation
        ]);
    }



    public function store(NewConsultationRequest $request){

        $validated = $request->validated();
        $NewConsultation = New_Consultation::create($validated);

        return response()->json([
            'message' => 'Successfully Saved',
            'consulatation' => $NewConsultation,
        ]);

    }
}
