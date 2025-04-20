<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DailyInventoryController;
use App\Http\Controllers\DailyTransactionsController;
use App\Http\Controllers\IndicatorLibraryController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\SystemUserController;


// DONE
Route::prefix('customers')->group(function () {
    Route::get('/', [CustomersController::class, 'index']);                                 // Fetch all customers
    Route::get('/{id}', [CustomersController::class, 'show']);                              // Fetch a single customer by ID
    Route::post('/', [CustomersController::class, 'store']);                                // Create a new customer
    Route::put('/{id}', [CustomersController::class, 'update']);                            // Update an existing customer by ID
    Route::delete('/{id}', [CustomersController::class, 'destroy']);                                      // Delete a customer by ID
});


Route::prefix('daily')->group(function () {
    Route::get('/', [DailyInventoryController::class, 'index']);                                           // Get all transactions
    Route::get('/{id}', [DailyInventoryController::class, 'show']);                                       // Get a specific transaction
    Route::get('/inventory/lowquantity', [DailyInventoryController::class, 'getLowQuantityStocks']);                  //Get low quantity stocks
    Route::get('/today/{transaction_date}', [DailyInventoryController::class, 'showTodayInventory']);    // Get transactions by date
    Route::get('/inventory/lastest', [DailyInventoryController::class, 'showLatest']);
    Route::post('/', [DailyInventoryController::class, 'store']);                                        // Create a new transaction
    Route::post('/inventory/open-latest', [DailyInventoryController::class, 'regenerateInventory']);            // regenerate inventory for the day || generate OPENNING ITEM LIST
    Route::post('/inventory/close-latest', [DailyInventoryController::class, 'closeInventory']);           // CLOSE ITEMS FOR THE DAY
    Route::put('/{id}', [DailyInventoryController::class, 'update']);                                  // Update an existing transaction
    Route::delete('/{id}', [DailyInventoryController::class, 'destroy']);                                           // Delete a transaction

    Route::get('/mode/test', [DailyInventoryController::class, 'testQuery']);
});


Route::prefix('orders')->group(function(){
    Route::get('/', [DailyTransactionsController::class,'index']);                      // Get all orders
    Route::get('/{id}', [DailyTransactionsController::class,'show']);                  // Get a specific orders
    Route::get('/transaction/new/{customer_id}',[DailyTransactionsController::class,'newTransactionID']);
    Route::post('/new', [DailyTransactionsController::class,'store']);                   // Create a new orders
    Route::put('/{id}', [DailyTransactionsController::class,'update']);              // Update a orders
    Route::get('/transaction/latest',[DailyTransactionsController::class,'getCustomersWithLatestTransactions']);
    Route::get('/transaction/{transaction_id}',[DailyTransactionsController::class,'showLatestOrder']); //show order of customer
    Route::get('/transaction/unique/{customer_id}',[DailyTransactionsController::class,'getTransactionID']); //show unique transaction numbers  of customer
    Route::delete('/order/{id}', [DailyTransactionsController::class,'destroy']);                       // Delete a orders
});


Route::prefix('items')->group(function () {
    Route::get('/', [ItemsController::class, 'index']);                      // Get all items
    Route::get('/{id}', [ItemsController::class, 'show']);                  // Get single item by ID
    Route::get('/po/show/{po_number}',[ItemsController::class,'showItemsByPO']); //Get all items By PO
    Route::get('/expire/list',[ItemsController::class,'getExpiringStock']); // Get all expiring item
    Route::post('/new', [ItemsController::class, 'store']);                  // Create a new item
    Route::put('/{id}', [ItemsController::class, 'update']);             // Update an item
    Route::delete('/{id}', [ItemsController::class, 'destroy']);                      // Delete an item by ID
    Route::delete('/po/remove/{po_number}', [ItemsController::class, 'destroyItemsByPO']);  // Delete items by PO number

    Route::get('/stock/list', [ItemsController::class, 'getJoinedItemswitInventory']);
    Route::get('/generate/tempno', [ItemsController::class, 'TemporaryID']);

});



Route::prefix('system-users')->group(function () {
    Route::get('/', [SystemUserController::class, 'index']);              // Get all users
    Route::get('/{id}', [SystemUserController::class, 'show']);          // Get a specific user
    Route::post('/', [SystemUserController::class, 'store']);           // Create a new user
    Route::put('/{id}', [SystemUserController::class, 'update']);      // Update an existing user
    Route::delete('/{id}', [SystemUserController::class, 'destroy']);               // Delete a user
});


Route::prefix('indicators')->group(function () {
    Route::post('/open', [IndicatorLibraryController::class, 'store']);          // Create a new indicators OPEN/CLOSE for today
    Route::put('/close', [IndicatorLibraryController::class, 'update']);        // Update an existing indicator
    Route::get('/current', [IndicatorLibraryController::class, 'getCurrentStatus']); //Get Indicator for Today
    //OPEN Indicator for Today
    //CLOSE Indicator for Today
});


