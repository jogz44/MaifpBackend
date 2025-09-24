<?php

use App\Http\Controllers\AssistanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\GuaranteeLetterController;
use App\Http\Controllers\NewConsultationController;
use App\Http\Controllers\UserCredentialsController;

Route::prefix('assistance')->group(function (){
    Route::post('/store', [AssistanceController::class, 'store']);
    Route::get('/', [AssistanceController::class, 'index']);
    Route::get('/funds', [AssistanceController::class, 'funds']);
});

Route::post('medications/status', [MedicationController::class, 'status']); // updating the status of the  transaction_id if Done
// Route::post('transactions/{id}/update/status/', [TransactionController::class, 'status_update']);
Route::get('activity',[SystemUserController::class,'activity_log']);
Route::get('/role', [SystemUserController::class, 'role']); // Get all roles
Route::post('/user/login', [SystemUserController::class, 'login_User']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('billing')->group(function () {
        // Route::post('/update/status/{transactionId}', [BillingController::class]);
        Route::get('/{transactionId}', [BillingController::class, 'billing']); // billing of the patient per transaction
        Route::get('/', [BillingController::class, 'index']); // fetch the patient  already done for his transaction
        Route::post('/store', [BillingController::class, 'store']); // storing the patient and his transaction on the billing table  to deduc the total_amount of patient billing on the remaining funds
    });

    Route::prefix('guarantee')->group(function () {
        // Route::post('/update/status/{transactionId}', [BillingController::class]);
        Route::get('/', [GuaranteeLetterController::class, 'index']); //  fetch the patient on the guarantee letter
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        // Route::get('/{id}', [PatientController::class, 'show']);
        Route::delete('/delete', [TransactionController::class, 'deleteAllTransactions']); // the all data on the transaction table
        // Route::put('/{id}/update/status/', [TransactionController::class, 'status_update']);  //updating transaction status if the patient are qualified or unqualified
        Route::post('/add', [TransactionController::class, 'addTransactionAndVitals']); // adding the patient transaction and vital
        Route::put('/update/{id}', [TransactionController::class, 'update']); //  updating the transaction of the patient
        Route::put('/representative/{id}', [TransactionController::class, 'rep_update']); //  updating the transaction of the patient
        Route::put('/vital/update/{id}', [TransactionController::class, 'vital_update']); // updating patient vital
        // Route::post('/update/{id}', [TransactionController::class, 'update']); //  updating the transaction of the patient
        Route::get('/qualified', [NewConsultationController::class, 'qualifiedTransactionsConsultation']);  // fetch all patient was qualified for the consulatation
        Route::get('/{id}', [TransactionController::class, 'show']); // fetching the transaction on his vital
        Route::put('/{id}/update/status/', [TransactionController::class, 'status_update']);

    });

    Route::prefix('medications')->group(function () {
        // Route::get('/', [MedicationController::class, 'qualifiedTransactionsMedication']); // fetching the patient need to go on the medication  base on the transaction_type and consultation
        Route::post('/store', [MedicationController::class, 'store']);
        // Route::post('/status', [MedicationController::class, 'status']); // updating the status of the  transaction_id if Done
        // Route::get('/transaction/status', [MedicationController::class, 'transaction_medication_status']); // fetching the transaction on his vital
        Route::post('/update', [MedicationController::class, 'status']); // fetching the transaction on his vital

    });

    Route::prefix('laboratory')->group(function () {
        Route::post('/store', [LaboratoryController::class, 'store']); // store the patient laboratory and amount
        Route::post('/status', [LaboratoryController::class, 'laboratory_status']); // store the patient laboratory and amount
        Route::get('/', [LaboratoryController::class, 'qualifiedTransactionsLaboratory']); // fetching the patient on the laboratory
        Route::get('/index/lab_services', [LaboratoryController::class, 'lib_laboratory_index']);
        Route::post('/store/lab_services', [LaboratoryController::class, 'lib_laboratory_store']);
        Route::post('/update/lab_services/{lib_laboratory}', [LaboratoryController::class, 'lib_laboratory_update']);
        Route::delete('/delete/lab_services/{lib_laboratory}', [LaboratoryController::class, 'lib_laboratory_delete']);
        Route::get('/exam/index', [LaboratoryController::class, 'lib_lab_index']); // store the patient laboratory and amount
        Route::post('/exam/store', [LaboratoryController::class, 'lib_lab_store']); // store the patient laboratory and amount
        Route::post('/exam/update/{lib_laboratory_examination_id}', [LaboratoryController::class, 'lib_lab_update']); // store the patient laboratory and amount
        Route::delete('/exam/delete/{lib_laboratory_examination_id}', [LaboratoryController::class, 'lib_lab_delete']); // store the patient laboratory and amount
        Route::get('/radiology/index', [LaboratoryController::class, 'lib_rad_index']); // store the patient laboratory and amount
        Route::post('/radiology/store', [LaboratoryController::class, 'lib_rad_store']); // store the patient laboratory and amount
        Route::post('/radiology/update/{lib_rad_id}', [LaboratoryController::class, 'lib_rad_update']); // store the patient laboratory and amount
        Route::delete('/radiology/delete/{lib_rad_id}', [LaboratoryController::class, 'lib_rad_delete']); // store the patient laboratory and amount

    });

    // Route::put('transactions/{id}/update/status/', [TransactionController::class, 'status_update']);

    Route::prefix('doctor')->group(function () {
        Route::get('/', [NewConsultationController::class, 'lib_doctor_index']); // fetch doctor fee
        Route::post('/store', [NewConsultationController::class, 'lib_doctor_store']); // add  doctor fee
        Route::post('/update/{lib_doctor}', [NewConsultationController::class, 'lib_doctor_update']); // update doctor fee
        Route::delete('/delete/{lib_doctor}', [NewConsultationController::class, 'lib_doctor_delete']); // delete doctor fee
    });

    Route::prefix('new_consultations')->group(function () {
        Route::post('/store', [NewConsultationController::class, 'store']); // this route is for the consultation of patient update if the transaction was exist if didnt exist create
        // Route::get('/return', [NewConsultationController::class, 'ReturnConsultation']); // fetch the patient return on the consultation galing sa laboratory
    });

    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index']); // list of patient
        Route::get('/master_list', [PatientController::class, 'getAllPatientsWithLatestTransaction']); // list of patient
        Route::put('/update/{id}', [PatientController::class, 'update']); // updating patient information
        // Route::put('/vital/update/{id}', [TransactionController::class, 'vital_update']); // updating patient vital
        Route::post('/store', [PatientController::class, 'storeAll']); // store the transaction and patient information and vital
        Route::get('/consultation/return', [NewConsultationController::class, 'ReturnConsultation']); // fetch the patient return on the consultation galing sa laboratory
        Route::get('/assessment', [PatientController::class, 'assessment']); // fetch patient for assessment on the social if this qualified or unqualified
        Route::get('/count/badge', [PatientController::class, 'total_count_badge']); // fetch patient for assessment on the social if this qualified or unqualified
        Route::get('/{id}', [PatientController::class, 'show']); // patient information and his transaction

    });

    Route::post('/user/logout', [SystemUserController::class, 'logoutUser']);
    Route::prefix('system')->group(function () {
        Route::get('/users', [SystemUserController::class, 'index']);              // Get all users
        Route::get('/user/profile/{id}', [SystemUserController::class, 'show']);          // Get a specific user
        Route::post('/user/new', [SystemUserController::class, 'store']);           // Create a new user
        Route::put('/user/profile-update/{id}', [SystemUserController::class, 'update']);      // Update an existing user
        Route::delete('/user/profile-remove/{id}', [SystemUserController::class, 'destroy']);               // Delete a user
        Route::put('/user/profile-deactivate/{id}', [SystemUserController::class, 'deactivateUser']); // deactivate user
        Route::put('/user/profile-activate/{id}', [SystemUserController::class, 'activateUser']); // deactivate user
        Route::post('/user/credentials', [SystemUserController::class, 'GetMyModule']); // Get user credentials


        Route::get('/user/credentials', [UserCredentialsController::class, 'index']);
        Route::get('/user/{id}/credentials', [UserCredentialsController::class, 'showByUserId']); // Get user credentials
        // Route::put('/user/{user_id}/credentials/{credential_id}', [UserCredentialsController::class, 'update']); // update user credentials
        Route::post('/user/credentials', [UserCredentialsController::class, 'store']); // store user credentials
        Route::delete('/user/credentials/{id}', [UserCredentialsController::class, 'destroy']); // delete user credentials
        // Route::get('/user/credentials/user/{user_id}', [UserCredentialsController::class, 'showByUserId']); // Get user credentials by user ID



        Route::get('/configuration/{id}', [ConfigurationsController::class, 'show']); // Get all config
        Route::post('/configuration', [ConfigurationsController::class, 'store']); // Insert new config
        Route::put('/configuration/{id}/config', [ConfigurationsController::class, 'updateConfig']); // Update config
        Route::delete('/configuration/{id}', [ConfigurationsController::class, 'destroy']); // Delete config

    });

});
