<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MAIFIPReportController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\GuaranteeLetterController;
use App\Http\Controllers\NewConsultationController;
use App\Http\Controllers\UserCredentialsController;
use App\Http\Requests\NewConsultationRequest;

Route::get('/test-broadcast', function () {
    $counts = app(\App\Services\BadgeService::class)->getBadgeCounts();
    broadcast(new \App\Events\BadgeUpdated($counts));
    return response()->json(['message' => 'Broadcasted', 'data' => $counts]);
});


Route::get('/test', [PatientController::class, 'test_database']);



Route::post('/user/login', [SystemUserController::class, 'login_User']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/role', [SystemUserController::class, 'role']); // Get all roles
    Route::get('activity', [SystemUserController::class, 'activity_log']);

    Route::prefix('assistance')->group(function () { // guarantee
        Route::get('/', [AssistanceController::class, 'index']);
        Route::post('/store', [AssistanceController::class, 'storeAssistance']);
        Route::get('/funds', [AssistanceController::class, 'funds']);
    });

    Route::prefix('report')->group(function () { // report
        Route::post('/store', [MAIFIPReportController::class, 'report']);
        Route::get('/', [MAIFIPReportController::class, 'report_index']);// list of patient have gl number 
        // Route::get('/list-of-patient', [MAIFIPReportController::class, 'medicalAssistanceReport']); // list of patient have gl number  v2 
        Route::post('/generate-doh', [MAIFIPReportController::class, 'dohReport']);
    });

    Route::prefix('billing')->group(function () {
        Route::get('/', [BillingController::class, 'index']); // fetch the patient  already done for his transaction
        Route::get('/report', [BillingController::class, 'billing_report']); // fetching the patient need to go on the medication  base on the transaction_type and consultation
        // Route::post('/store', [BillingController::class, 'store']);
        Route::get('/{transactionId}', [BillingController::class, 'billingPatient']); // billing of the patient per transaction
    });

    Route::prefix('guarantee')->group(function () {
        Route::get('/', [GuaranteeLetterController::class, 'index']); //  fetch the patient on the guarantee letter
        Route::get('/{transactionId}', [GuaranteeLetterController::class, 'guaranteeLetter']); //  fetch the patient on the guarantee letter
        Route::post('/{transactionId}', [GuaranteeLetterController::class, 'update']); //  fetch the patient on the guarantee letter
        Route::post('update/status/{transactionId}', [GuaranteeLetterController::class, 'update_status']); //  fetch the patient on the guarantee letter
        Route::get('/max/number', [GuaranteeLetterController::class, 'getMaxGLNumber']);
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::delete('/delete', [TransactionController::class, 'deleteAllTransactions']); // the all data on the transaction table
        Route::post('/add', [TransactionController::class, 'addTransactionAndVitals']); // adding the patient transaction and vital
        Route::post('/update/{id}', [TransactionController::class, 'update']); //  updating the transaction of the patient
        Route::post('/representative/{id}', [TransactionController::class, 'rep_update']); //  updating the transaction of the patient
        Route::post('/vital/update/{id}', [TransactionController::class, 'vital_update']); // updating patient vital
        Route::get('/qualified', [NewConsultationController::class, 'qualifiedTransactionsConsultation']);  // fetch all patient was qualified for the consulatation
        Route::get('/{id}', [TransactionController::class, 'show']); // fetching the transaction on his vital
        Route::post('/{id}/update/status/', [TransactionController::class, 'updateTransaction']); // billing updating
        Route::post('/{TransactionId}/update/philhealth', [TransactionController::class, 'status_to_maifip']);// updating the transaction to maifip
    });

    Route::prefix('laboratory')->group(function () {
        // Route::get('v2/', [LaboratoryController::class, 'patientLaboratory']); // fetching the patient on the laboratory

        Route::post('/store', [LaboratoryController::class, 'storeLaboratory']); //
        Route::delete('/delete', [LaboratoryController::class, 'destroy']);

        Route::post('/status', [LaboratoryController::class, 'updateLaboratorystatus']);
        // Route::get('/', [LaboratoryController::class, 'qualifiedTransactionsLaboratory']); // fetching the patient on the laboratory
        Route::get('/', [LaboratoryController::class, 'patientLaboratory']); // fetching the patient on the laboratory

        Route::get('/index/lab_services', [LaboratoryController::class, 'lib_laboratory_index']);
        Route::post('/store/lab_services', [LaboratoryController::class, 'lib_laboratory_store']);

        Route::post('/update/lab_services/{lib_laboratory}', [LaboratoryController::class, 'lib_laboratory_update']);
        Route::delete('/delete/lab_services/{lib_laboratory}', [LaboratoryController::class, 'lib_laboratory_delete']);

        // lib examination
        Route::get('/exam/index', [LaboratoryController::class, 'lib_lab_index']); //
        Route::post('/exam/store', [LaboratoryController::class, 'lib_lab_store']); //
        Route::post('/exam/update/{lib_laboratory_examination_id}', [LaboratoryController::class, 'lib_lab_update']); //
        Route::delete('/exam/delete/{lib_laboratory_examination_id}', [LaboratoryController::class, 'lib_lab_delete']); //
        Route::get('/exam/{transaction}', [LaboratoryController::class, 'getByTransaction_exam']); //

        // lib radiology
        Route::get('/radiology/index', [LaboratoryController::class, 'lib_rad_index']); //
        Route::get('/radiology/{transaction}', [LaboratoryController::class, 'getByTransaction']); //

        Route::post('/radiology/store', [LaboratoryController::class, 'lib_rad_store']); //
        Route::post('/radiology/update/{lib_rad_id}', [LaboratoryController::class, 'lib_rad_update']); //
        Route::delete('/radiology/delete/{lib_rad_id}', [LaboratoryController::class, 'lib_rad_delete']); //
        // lib ultra sound
        Route::get('/ultrasound/index', [LaboratoryController::class, 'lib_ultra_sound_index']); //
        Route::post('/ultrasound/store', [LaboratoryController::class, 'lib_ultra_sound_store']); //
        Route::post('/ultrasound/update/{lib_ultra_sound_id}', [LaboratoryController::class, 'lib_ultra_sound_update']); //
        Route::get('/ultrasound/{transaction}', [LaboratoryController::class, 'getByTransaction_ultrasound']); //
        Route::delete('/ultrasound/delete/{lib_ultra_sound_id}', [LaboratoryController::class, 'lib_ultra_sound_delete']); //
        // lib mammogram
        Route::get('/mammogram/index', [LaboratoryController::class, 'lib_mammogram_index']); //
        Route::post('/mammogram/store', [LaboratoryController::class, 'lib_mammogram_store']); //
        Route::post('/mammogram/update/{lib_mammogram_id}', [LaboratoryController::class, 'lib_mammogram_update']); //
        Route::delete('/mammogram/delete/{lib_mammogram_id}', [LaboratoryController::class, 'lib_mammogram_delete']); //
        Route::get('/mammogram/{transaction}', [LaboratoryController::class, 'getByTransaction_mammogram']); //
        Route::get('/{transactionId}', [LaboratoryController::class, 'laboratory_transaction']); //

    });

    Route::prefix('doctor')->group(function () {
        Route::get('/', [NewConsultationController::class, 'lib_doctor_index']); // fetch doctor fee
        Route::post('/store', [NewConsultationController::class, 'lib_doctor_store']); // add  doctor fee
        Route::post('/update/{lib_doctor}', [NewConsultationController::class, 'lib_doctor_update']); // update doctor fee
        Route::delete('/delete/{lib_doctor}', [NewConsultationController::class, 'lib_doctor_delete']); // delete doctor fee
    });

    Route::prefix('new_consultations')->group(function () {
        Route::post('/store', [NewConsultationController::class, 'store']); // this route is for the consultation of patient update if the transaction was exist if didnt exist create
    });

    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index']); // list of patient
        Route::get('/master_list', [PatientController::class, 'getAllPatientsWithLatestTransaction']); // list of patient
        Route::post('/update/{id}', [PatientController::class, 'updatePatient']); // updating patient information
        Route::post('/store', [PatientController::class, 'storePatient']); // store the transaction and patient information and vital
        Route::get('/consultation/return', [NewConsultationController::class, 'ReturnConsultation']); // fetch the patient return on the consultation galing sa laboratory
        Route::get('/assessment', [PatientController::class, 'assessment']); // fetch patient for assessment on the social if this qualified or unqualified

        Route::get('/count/badge', [PatientController::class, 'total_count_badge']); // fetch patient for assessment on the social if this qualified or unqualified
        Route::get('/{id}', [PatientController::class, 'show']); // patient information and his transaction
        Route::get('/philhealth/assessment', [PatientController::class, 'philhealth_assessment']); // fetch patient for assessment on the social if this qualified or unqualified
        Route::get('/philhealth/maifip/assessment', [PatientController::class, 'philhealth_to_maifip_assessment']); // phildhealth to maifip assessment
        Route::get('/consultation/list', [NewConsultationController::class, 'patients_consultation_list']); // fetch patient consultation list

    });

    Route::post('/user/logout', [SystemUserController::class, 'logoutUser']);

    Route::prefix('system')->group(function () {
        Route::get('/users', [SystemUserController::class, 'index']);              // Get all users
        Route::get('/user/profile/{id}', [SystemUserController::class, 'show']);          // Get a specific user
        Route::post('/user/new', [SystemUserController::class, 'store']);           // Create a new user
        Route::post('/user/profile-update/{id}', [SystemUserController::class, 'update']);      // Update an existing user
        Route::delete('/user/profile-remove/{id}', [SystemUserController::class, 'destroy']);               // Delete a user
        Route::post('/user/profile-deactivate/{id}', [SystemUserController::class, 'deactivateUser']); // deactivate user
        Route::post('/user/profile-activate/{id}', [SystemUserController::class, 'activateUser']); // deactivate user
        Route::post('/user/credentials', [SystemUserController::class, 'GetMyModule']); // Get user credentials
        Route::get('/user/credentials', [UserCredentialsController::class, 'index']);
        Route::get('/user/{id}/credentials', [UserCredentialsController::class, 'showByUserId']); // Get user credentials
        // Route::post('/user/{user_id}/credentials/{credential_id}', [UserCredentialsController::class, 'update']); // update user credentials
        Route::post('/user/credentials', [UserCredentialsController::class, 'store']); // store user credentials
        Route::delete('/user/credentials/{id}', [UserCredentialsController::class, 'destroy']); // delete user credentials
        // Route::get('/user/credentials/user/{user_id}', [UserCredentialsController::class, 'showByUserId']); // Get user credentials by user ID
        Route::get('/configuration/{id}', [ConfigurationsController::class, 'show']); // Get all config
        Route::post('/configuration', [ConfigurationsController::class, 'store']); // Insert new config
        Route::post('/configuration/{id}/config', [ConfigurationsController::class, 'updateConfig']); // Update config
        Route::delete('/configuration/{id}', [ConfigurationsController::class, 'destroy']); // Delete config

    });

    Route::prefix('medications')->group(function () {
        Route::get('/v2', [MedicationController::class, 'patientMedication']);
        Route::get('/', [MedicationController::class, 'index_view']); // fetching the patient need to go on the medication  base on the transaction_type and consultation
        Route::post('/store', [MedicationController::class, 'store']);
        Route::post('/update', [MedicationController::class, 'status']); // fetching the transaction on his vital

    });

});
