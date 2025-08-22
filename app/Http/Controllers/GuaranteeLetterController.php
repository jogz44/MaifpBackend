<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuaranteeLetterRequest;
use App\Models\GuaranteeLetter;
use Carbon\Carbon;
use App\Models\Patient;
use Illuminate\Http\Request;

class GuaranteeLetterController extends Controller
{
    //

    // public function index()
    // {
    //     $patients = Patient::whereHas('transaction', function ($query) {
    //         $query->whereDate('transaction_date', Carbon::today())
    //             ->where('status', 'Complete');
    //     })
    //         ->with([

    //             'transaction' => function ($q) {
    //                 $q->whereDate('transaction_date', Carbon::today())
    //                     ->where('status', 'Complete');
    //             }
    //         ])
    //         ->get([
    //             'id',
    //             'firstname',
    //             'lastname',
    //             'middlename',
    //             'ext',
    //             'birthdate',
    //             'age',
    //             'contact_number',
    //             'barangay'
    //         ]);

    //     return response()->json($patients);
    // }
    public function index()
    {
        $patients = Patient::whereHas('transaction', function ($query) {
            $query->whereDate('transaction_date', Carbon::today())
                ->where('status', 'Complete');
        })
            ->whereDoesntHave('transaction.guaranteeLetter', function ($query) {
                $query->where('status', 'Funded');
            })
            ->with([
                'transaction' => function ($q) {
                    $q->whereDate('transaction_date', Carbon::today())
                        ->where('status', 'Complete');
                }
            ])
            ->get([
                'id',
                'firstname',
                'lastname',
                'middlename',
                'ext',
                'birthdate',
                'age',
                'contact_number',
                'barangay'
            ]);

        return response()->json($patients);
    }


    public function store (GuaranteeLetterRequest $request){ // store for guarantee letter

        $validated = $request->validated();

        $guarantee = GuaranteeLetter::updateOrCreate(
            ['transaction_id' => $validated['transaction_id']], // match condition
            $validated                                          // values to update
        );


        return response()->json($guarantee);


    }
}
