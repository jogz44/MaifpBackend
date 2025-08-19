<?php


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UnitController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosageTypeController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ConfigurationsController;
use App\Http\Controllers\DailyInventoryController;
use App\Http\Controllers\MedicinelibraryController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\UserCredentialsController;
use App\Http\Controllers\IndicatorLibraryController;
use App\Http\Controllers\DailyTransactionsController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\NewConsultationController;
use App\Http\Controllers\RequisitionIssuanceSlipController;
use App\Models\New_Consultation;

Route::get('/role', [SystemUserController::class, 'role']); // Get all roles
Route::post('/user/login', [SystemUserController::class, 'login_User']);
Route::get('patients/consultation/return', [NewConsultationController::class, 'ReturnConsultation']);

Route::get('laboratory', [TransactionController::class, 'qualifiedTransactionsLaboratory']);
Route::get('patients/assessment', [PatientController::class, 'assessment']);

Route::get('transactions/qualified', [TransactionController::class, 'qualifiedTransactionsConsultation']);
Route::get('transactions/{transactionId}', [BillingController::class, 'billing']);

Route::post('laboratory/store', [LaboratoryController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

Route::post('/user/logout', [SystemUserController::class, 'logoutUser']);

    Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']);
        // Route::get('/assessment', [PatientController::class, 'assessment']);              // Fetch all patients
        Route::get('/{id}', [PatientController::class, 'show']);     // Fetch a single patient by ID
        Route::put('/update/{id}', [PatientController::class, 'update']);
        Route::put('/vital/update/{id}', [TransactionController::class, 'vital_update']);
        Route::post('/store', [PatientController::class, 'storeAll']);
});

    Route::prefix('transactions')->group(function () {
        Route::get('/', [PatientController::class, 'index']);                                 // Fetch all patients
        // Route::get('/{id}', [PatientController::class, 'show']);
        // Route::get('/qualified', [TransactionController::class, 'qualifiedTransactionsConsultation']);
        // Route::get('/laboratory', [TransactionController::class, 'qualifiedTransactionsLaboratory']);
        // Route::get('/Medication', [TransactionController::class, 'qualifiedTransactionsMedication']);
        Route::delete('/delete', [TransactionController::class, 'deleteAllTransactions']);

        Route::put('/{id}/update/status/', [TransactionController::class, 'status_update']);
        // Route::post('/{id}/update/status/', [TransactionController::class, 'status_update']);

        Route::post('/add', [PatientController::class, 'addTransactionAndVitals']);                        // Fetch a single patient by ID
        Route::put('/update/{id}', [TransactionController::class, 'update']);
        Route::get('/{id}', [TransactionController::class, 'show']);
    });


    Route::prefix('new_consultations')->group(function () {
        Route::get('/', [NewConsultationController::class, 'index']);
        Route::get('/show/{id}', [NewConsultationController::class, 'show']);                                   // Fetch all patients
        Route::post('/store', [NewConsultationController::class, 'store']);

    });

    // Route::prefix('laboratory')->group(function () {
    //     Route::get('/', [NewConsultationController::class, 'index']);
    //     Route::get('/show/{id}', [NewConsultationController::class, 'show']);                                   // Fetch all patients
    //     Route::post('/store', [NewConsultationController::class, 'store']);
    // });


    Route::prefix('Budgets')->group(function () {
        Route::get('/', [BudgetController::class, 'index']);                                 // Fetch all transaction types
        Route::get('/{id}', [BudgetController::class, 'show']);                              // Fetch a single transaction type by ID
        Route::post('/store', [BudgetController::class, 'store']);                             // Create a new transaction type
        Route::post('/release/{id}', [BudgetController::class, 'releaseFunds']);
        Route::post('/additional', [BudgetController::class, 'additionalFunds']);
        Route::delete('/delete/{id}', [BudgetController::class, 'destroy']);
    });

    Route::prefix('transaction-types')->group(function (){
        Route::get('/', [TransactionTypeController::class, 'index']);                                 // Fetch all transaction types
        Route::get('/{id}', [TransactionTypeController::class, 'show']);                              // Fetch a single transaction type by ID
        Route::post('/store', [TransactionTypeController::class, 'store']);                             // Create a new transaction type
        Route::put('/update/{id}', [TransactionTypeController::class, 'update']);                      // Update an existing transaction type by ID
        Route::delete('/delete/{id}', [TransactionTypeController::class, 'destroy']);

    });

    Route::prefix('customers')->group(function () {
    Route::get('/', [CustomersController::class, 'index']);                                 // Fetch all customers
    Route::get('/{id}', [CustomersController::class, 'show']);                              // Fetch a single customer by ID
    Route::post('/', [CustomersController::class, 'store']);                                // Create a new customer
    Route::put('/{id}', [CustomersController::class, 'update']);                            // Update an existing customer by ID
    Route::delete('/{id}', [CustomersController::class, 'destroy']);
    Route::get('/transactions/{id}',[DailyTransactionsController::class,'Customer_Transaction_List']);
    Route::get('/transactions/{id}/list/{trans_id}',[DailyTransactionsController::class,'Customer_Transaction_List_Breakdown']);
    Route::post('/list/dates',[CustomersController::class, 'CustomerByDate']);
});


Route::prefix('daily')->group(function () {
    Route::get('/', [DailyInventoryController::class, 'index']);                                           // Get all transactions
    Route::get('/{id}', [DailyInventoryController::class, 'show']);                                       // Get a specific transaction
    Route::get('/inventory/lowquantity/{threshold}', [DailyInventoryController::class, 'getLowQuantityStocks']);                  //Get low quantity stocks
    Route::get('/inventory/emptyquantity', [DailyInventoryController::class, 'getEmptyQuantityStocks']);
    Route::get('/today/{transaction_date}', [DailyInventoryController::class, 'showTodayInventory']);    // Get transactions by date
    Route::get('/inventory/lastest', [DailyInventoryController::class, 'showLatest']);
    Route::post('/', [DailyInventoryController::class, 'store']);                                        // Create a new transaction
    Route::post('/inventory/open-latest/{id}', [DailyInventoryController::class, 'regenerateInventory']);            // regenerate inventory for the day || generate OPENNING ITEM LIST
    Route::post('/inventory/close-latest/{id}', [DailyInventoryController::class, 'closeInventory']);           // CLOSE ITEMS FOR THE DAY
    Route::get('/inventory/get-list/{date}', [DailyInventoryController::class, 'closeInventoryByDate']);           // CLOSE ITEMS FOR THE DAY
    Route::put('/{id}', [DailyInventoryController::class, 'update']);                                  // Update an existing transaction
    Route::delete('/{id}', [DailyInventoryController::class, 'destroy']);                                           // Delete a transaction

    Route::get('/mode/test', [DailyInventoryController::class, 'testQuery']);
    Route::get('/inventoryOpen/today', [DailyInventoryController::class, 'OpenTransactionLookUp']);

    Route::post('/ris/new',[RequisitionIssuanceSlipController::class, 'store']);
    Route::get('/ris/list',[RequisitionIssuanceSlipController::class, 'index']);
    Route::post('/ris',[RequisitionIssuanceSlipController::class, 'show']);
    Route::put('/ris/update',[RequisitionIssuanceSlipController::class, 'update']);
    Route::post('/ris/transactions',[RequisitionIssuanceSlipController::class, 'RIS']);
    Route::post('/ris/info',[RequisitionIssuanceSlipController::class, 'RIS_INFO']);
    Route::post('/ris/date',[RequisitionIssuanceSlipController::class, 'RIS_TransactionDate']);
});


Route::prefix('orders')->group(function(){
    Route::get('/', [DailyTransactionsController::class,'index']);                      // Get all orders
    Route::get('/{id}', [DailyTransactionsController::class,'show']);                  // Get a specific orders
    Route::get('/transaction/new/{customer_id}',[DailyTransactionsController::class,'newTransactionID']);
    Route::post('/new', [DailyTransactionsController::class,'store']);                   // Create a new orders
    Route::put('/{id}', [DailyTransactionsController::class,'update']);              // Update a orders
    Route::get('/transaction/latest/{date}',[DailyTransactionsController::class,'getCustomersWithTransactionsToday']);
    Route::get('/transaction/{transaction_id}',[DailyTransactionsController::class,'showLatestOrder']); //show order of customer
    Route::get('/transaction/unique/{customer_id}',[DailyTransactionsController::class,'getTransactionID']); //show unique transaction numbers  of customer
    Route::delete('/order/{id}', [DailyTransactionsController::class,'destroy']);                       // Delete a orders
});


Route::prefix('items')->group(function () {
    Route::post('/', [ItemsController::class, 'index']);                      // Get all items
    Route::get('/{id}', [ItemsController::class, 'show']);                  // Get single item by ID
    Route::get('/po/show/{po_number}',[ItemsController::class,'showItemsByPO']); //Get all items By PO
    Route::get('/expire/list',[ItemsController::class,'getExpiringStock']); // Get all expiring item
    Route::post('/new', [ItemsController::class, 'store']);                  // Create a new item
    Route::post('/batch/new', [ItemsController::class, 'batchstore']);
    Route::put('/{id}', [ItemsController::class, 'update']);             // Update an item
    Route::delete('/{id}', [ItemsController::class, 'destroy']);                      // Delete an item by ID
    Route::delete('/po/remove/{po_number}', [ItemsController::class, 'destroyItemsByPO']);  // Delete items by PO number
    Route::get('/stock/filteredlist', [ItemsController::class, 'getJoinedItemswitInventoryfiltered']);
    Route::get('/stock/list', [ItemsController::class, 'getJoinedItemswitInventory']);
    Route::get('/generate/tempno', [ItemsController::class, 'TemporaryID']);
    Route::get('/temp/po',[ItemsController::class,'TempPOlist']); // Get all temporary items
    Route::put('/temp/po/{tempno}', [ItemsController::class,'UpdateTempPO']); // Update temporary P.O.
    Route::post('/stockcard', [ItemsController::class, 'stockCard']); // Get stock card for an item
    Route::post('/inventory/bydate',[ItemsController::class,'InventoryRangeDate']);
});


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

    Route::get('/library/medlist', [MedicinelibraryController::class, 'index']); // Get all units
    Route::post('/library/medlist/new', [MedicinelibraryController::class, 'batch_Store']); // Get all units
    Route::get('/library/items', [ItemsController::class, 'itemList']); // Get all units

    Route::get('/library/units', [UnitController::class, 'getUnits']); // Get all units
    Route::post('/library/units', [UnitController::class, 'store']); // Insert new unit
    Route::get('/library/units/{id}', [UnitController::class, 'show']); // Get single unit by ID
    Route::put('/library/units/{id}', [UnitController::class, 'update']); // Update a unit
    Route::delete('/library/units/{id}', [UnitController::class, 'destroy']); // Delete a unit

    Route::get('/library/dosages', [DosageTypeController::class, 'getDosageTypes']); // Get all units
    Route::post('/library/dosages', [DosageTypeController::class, 'store']); // Insert new unit
    Route::get('/library/dosages/{id}', [DosageTypeController::class, 'show']); // Get single unit by ID
    Route::put('/library/dosages/{id}', [DosageTypeController::class, 'update']); // Update a unit
    Route::post('/library/dosages/remove', [DosageTypeController::class, 'removeDosageType']);
    // Route::delete('/library/dosages/{id}', [DosageTypeController::class, 'destroy']);  Delete a unit


    Route::get('/configuration/{id}', [ConfigurationsController::class, 'show']); // Get all config
    Route::post('/configuration', [ConfigurationsController::class, 'store']); // Insert new config
    Route::put('/configuration/{id}/config', [ConfigurationsController::class, 'updateConfig']); // Update config
    Route::delete('/configuration/{id}', [ConfigurationsController::class, 'destroy']); // Delete config

});


Route::prefix('indicators')->group(function () {
    Route::post('/open', [IndicatorLibraryController::class, 'store']);          // Create a new indicators OPEN/CLOSE for today
    Route::put('/close', [IndicatorLibraryController::class, 'update']);        // Update an existing indicator
    Route::get('/current', [IndicatorLibraryController::class, 'getCurrentStatus']); //Get Indicator for Today

});


Route::prefix('reports')->group(function () {
    Route::get('/dispense/monthly', [ReportsController::class, 'Monthly_Dispense']);          // get dispense report
    Route::get('/dispense/recipient', [ReportsController::class, 'Recipients_Report']);          // get dispense recipient report
    Route::get('/dispense/yearly/{year}', [ReportsController::class, 'Monthly_Dispense_By_Year']);           // get dispense yearly report
});

Route::prefix('dashboard')->group(function () {
    Route::get('/medicines/activeStocks', [DashboardController::class, 'dashboard_medicines_instock']);          // get dispense report
    Route::get('/medicines/expiredStocks', [DashboardController::class, 'dashboard_medicines_expired']);          // get dispense recipient report
    Route::get('/medicines/noStocks', [DashboardController::class, 'dashboard_medicines_outOfStock']);           // get dispense yearly report
    Route::get('/medicines/LowStocks/{threshold}', [DashboardController::class, 'getLowQuantityStocks']);           // get dispense yearly report
    Route::get('/medicines/temporary', [DashboardController::class, 'dashboard_medicines_countTemp']);           // get dispense yearly report
    Route::get('/medicines/ten', [DashboardController::class, 'dashboard_medicines_TopTen']);           // get dispense yearly report

    Route::get('/customers/registered', [DashboardController::class, 'dashboard_registered_customers']);           // get dispense yearly report
    Route::get('/customers/served', [DashboardController::class, 'dashboard_served_customers']);           // get dispense yearly report
    Route::get('/customers/perbrgy', [DashboardController::class, 'dashboard_customers_barangay']);           // get dispense yearly report
    Route::get('/customers/age', [DashboardController::class, 'dashboard_customers_ages']);           // get dispense yearly report
    Route::get('/customers/gender', [DashboardController::class, 'dashboard_customers_genders']);           // get dispense yearly report

});

Route::prefix('logs')->group(function () {
    Route::get('/audit/logs', [AuditController::class, 'index']); // Get all audit logs
    Route::post('/audit/logs', [AuditController::class, 'store']); // Store a new audit log
    Route::get('/audit/logs/{id}', [AuditController::class, 'show']); // Show a specific audit log
    Route::put('/audit/logs/{id}', [AuditController::class, 'update']); // Update a specific audit log
    Route::delete('/audit/logs/{id}', [AuditController::class, 'destroy']); // Delete a specific audit log
});

});
