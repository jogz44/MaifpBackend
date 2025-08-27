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
use App\Http\Controllers\GuaranteeLetterController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\NewConsultationController;
use App\Http\Controllers\RequisitionIssuanceSlipController;
use App\Models\Medication;

Route::put('transactions/{id}/update/status/', [TransactionController::class, 'status_update']);
// Route::get('patients/assessment', [PatientController::class, 'assessment']); // fetch patient for assessment on the social if this qualified or unqualified

// Route::post('transactions/{id}/update/status/', [TransactionController::class, 'status_update']);
Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']); // list of patient
    Route::put('/update/{id}', [PatientController::class, 'update']); // updating patient information
    Route::put('/vital/update/{id}', [TransactionController::class, 'vital_update']); // updating patient vital
    Route::post('/store', [PatientController::class, 'storeAll']); // store the transaction and patient information and vital
    Route::get('/consultation/return', [NewConsultationController::class, 'ReturnConsultation']); // fetch the patient return on the consultation galing sa laboratory
    Route::get('/assessment', [PatientController::class, 'assessment']); // fetch patient for assessment on the social if this qualified or unqualified
    Route::get('/{id}', [PatientController::class, 'show']); // patient information and his transaction

});

Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    // Route::get('/{id}', [PatientController::class, 'show']);
    Route::delete('/delete', [TransactionController::class, 'deleteAllTransactions']); // the all data on the transaction table
    // Route::put('/{id}/update/status/', [TransactionController::class, 'status_update']);  //updating transaction status if the patient are qualified or unqualified
    Route::post('/add', [PatientController::class, 'addTransactionAndVitals']); // adding the patient transaction and vital
    Route::put('/update/{id}', [TransactionController::class, 'update']); //  updating the transaction of the patient
    // Route::post('/update/{id}', [TransactionController::class, 'update']); //  updating the transaction of the patient

    Route::get('/qualified', [TransactionController::class, 'qualifiedTransactionsConsultation']);  // fetch all patient was qualified for the consulatation
    Route::get('/{id}', [TransactionController::class, 'show']); // fetching the transaction on his vital

});

Route::prefix('new_consultations')->group(function () {
    Route::post('/store', [NewConsultationController::class, 'store']); // this route is for the consultation of patient update if the transaction was exist if didnt exist create
});

Route::prefix('medications')->group(function () {
    Route::get('/', [MedicationController::class, 'qualifiedTransactionsMedication']);// fetching the patient need to go on the medication  base on the transaction_type and consultation
    Route::post('/store', [MedicationController::class, 'store']);
    // Route::post('/status/{transactionId}', [MedicationController::class, 'status']); // updating the status of the  transaction_id if Done
    // Route::get('/transaction/status', [MedicationController::class, 'transaction_medication_status']); // fetching the transaction on his vital
    Route::post('/update', [MedicationController::class, 'status']); // fetching the transaction on his vital

});

Route::prefix('laboratory')->group(function () {
    Route::post('/store', [LaboratoryController::class, 'store']); // store the patient laboratory and amount
    Route::post('/update/{id}', [LaboratoryController::class, 'status']); // store the patient laboratory and amount
    Route::get('/', [TransactionController::class, 'qualifiedTransactionsLaboratory']); // fetching the patient on the laboratory

});

Route::prefix('billing')->group(function () {
    // Route::post('/update/status/{transactionId}', [BillingController::class]);
    Route::get('/{transactionId}', [BillingController::class, 'billing']); // billing of the patient per transaction
    Route::get('/', [BillingController::class, 'index']); // fetch the patient  already done for his transaction
    Route::post('/store',[BillingController::class,'store']); // storing the patient and his transaction on the billing table  to deduc the total_amount of patient billing on the remaining funds

});

Route::prefix('guarantee')->group(function () {
    // Route::post('/update/status/{transactionId}', [BillingController::class]);
    Route::get('/', [GuaranteeLetterController::class, 'index']); //  fetch the patient on the guarantee letter
    Route::post('/store', [GuaranteeLetterController::class, 'store']); // billing of the patient per transaction

});

Route::prefix('Budgets')->group(function () {
    Route::get('/', [BudgetController::class, 'index']);
    Route::get('/dashboard', [BudgetController::class, 'dashboardBudget']);
    Route::post('/store', [BudgetController::class, 'store']);
    Route::delete('/delete/{id}', [BudgetController::class, 'destroy']);
    Route::get('/funded', [BudgetController::class, 'list_of_funded']);
    Route::get('/{id}', [BudgetController::class, 'show']);
});

Route::get('/role', [SystemUserController::class, 'role']); // Get all roles
Route::post('/user/login', [SystemUserController::class, 'login_User']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/logout', [SystemUserController::class, 'logoutUser']);


    Route::prefix('transaction-types')->group(function () {
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
        Route::get('/transactions/{id}', [DailyTransactionsController::class, 'Customer_Transaction_List']);
        Route::get('/transactions/{id}/list/{trans_id}', [DailyTransactionsController::class, 'Customer_Transaction_List_Breakdown']);
        Route::post('/list/dates', [CustomersController::class, 'CustomerByDate']);
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

        Route::post('/ris/new', [RequisitionIssuanceSlipController::class, 'store']);
        Route::get('/ris/list', [RequisitionIssuanceSlipController::class, 'index']);
        Route::post('/ris', [RequisitionIssuanceSlipController::class, 'show']);
        Route::put('/ris/update', [RequisitionIssuanceSlipController::class, 'update']);
        Route::post('/ris/transactions', [RequisitionIssuanceSlipController::class, 'RIS']);
        Route::post('/ris/info', [RequisitionIssuanceSlipController::class, 'RIS_INFO']);
        Route::post('/ris/date', [RequisitionIssuanceSlipController::class, 'RIS_TransactionDate']);
    });


    Route::prefix('orders')->group(function () {
        Route::get('/', [DailyTransactionsController::class, 'index']);                      // Get all orders
        Route::get('/{id}', [DailyTransactionsController::class, 'show']);                  // Get a specific orders
        Route::get('/transaction/new/{customer_id}', [DailyTransactionsController::class, 'newTransactionID']);
        Route::post('/new', [DailyTransactionsController::class, 'store']);                   // Create a new orders
        Route::put('/{id}', [DailyTransactionsController::class, 'update']);              // Update a orders
        Route::get('/transaction/latest/{date}', [DailyTransactionsController::class, 'getCustomersWithTransactionsToday']);
        Route::get('/transaction/{transaction_id}', [DailyTransactionsController::class, 'showLatestOrder']); //show order of customer
        Route::get('/transaction/unique/{customer_id}', [DailyTransactionsController::class, 'getTransactionID']); //show unique transaction numbers  of customer
        Route::delete('/order/{id}', [DailyTransactionsController::class, 'destroy']);                       // Delete a orders
    });


    Route::prefix('items')->group(function () {
        Route::post('/', [ItemsController::class, 'index']);                      // Get all items
        Route::get('/{id}', [ItemsController::class, 'show']);                  // Get single item by ID
        Route::get('/po/show/{po_number}', [ItemsController::class, 'showItemsByPO']); //Get all items By PO
        Route::get('/expire/list', [ItemsController::class, 'getExpiringStock']); // Get all expiring item
        Route::post('/new', [ItemsController::class, 'store']);                  // Create a new item
        Route::post('/batch/new', [ItemsController::class, 'batchstore']);
        Route::put('/{id}', [ItemsController::class, 'update']);             // Update an item
        Route::delete('/{id}', [ItemsController::class, 'destroy']);                      // Delete an item by ID
        Route::delete('/po/remove/{po_number}', [ItemsController::class, 'destroyItemsByPO']);  // Delete items by PO number
        Route::get('/stock/filteredlist', [ItemsController::class, 'getJoinedItemswitInventoryfiltered']);
        Route::get('/stock/list', [ItemsController::class, 'getJoinedItemswitInventory']);
        Route::get('/generate/tempno', [ItemsController::class, 'TemporaryID']);
        Route::get('/temp/po', [ItemsController::class, 'TempPOlist']); // Get all temporary items
        Route::put('/temp/po/{tempno}', [ItemsController::class, 'UpdateTempPO']); // Update temporary P.O.
        Route::post('/stockcard', [ItemsController::class, 'stockCard']); // Get stock card for an item
        Route::post('/inventory/bydate', [ItemsController::class, 'InventoryRangeDate']);
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
