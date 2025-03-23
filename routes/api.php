<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DailyInventoryController;
use App\Http\Controllers\DailyTransactionsController;
use App\Http\Controllers\ItemsController;



Route::prefix('customers')->group(function () {
    Route::get('/', [CustomersController::class, 'index']); // Fetch all customers
    Route::get('/{id}', [CustomersController::class, 'show']); // Fetch a single customer by ID
    Route::post('/', [CustomersController::class, 'store']); // Create a new customer
    Route::put('/{id}', [CustomersController::class, 'update']); // Update an existing customer by ID
    Route::delete('/{id}', [CustomersController::class, 'destroy']); // Delete a customer by ID
});





Route::controller(DailyInventoryController::class)->group(function () {
    Route::get('daily', 'index');                       // Get all transactions
    Route::get('daily/{id}', 'show');                   // Get a specific transaction
    Route::get('daily/lowquantity', 'getLowQuantityStocks');
    Route::get('daily/today/{transaction_date}', 'showTodayInventory'); // Get transactions by date
    Route::post('daily', 'store');                      // Create a new transaction
    Route::put('daily/{id}', 'update');                 // Update an existing transaction
    Route::delete('daily/{id}', 'destroy');             // Delete a transaction
});



Route::controller(DailyTransactionsController::class)->group(function () {
    Route::get('orders', 'index');                   // Get all orders
    Route::get('orders/{id}', 'show');                // Get a specific orders
    Route::post('orders', 'store');                   // Create a new orders
    Route::put('orders/{id}', 'update');              // Update a orders
    Route::delete('orders/{id}', 'destroy');          // Delete a orders
});



Route::prefix('items')->group(function () {
    Route::get('/', [ItemsController::class, 'index']);           // Get all items
    Route::get('/{id}', [ItemsController::class, 'show']);        // Get single item by ID
    Route::get('/expiring',[ItemsController::class,'getExpiringStock']);  // Get all expiring item

    Route::post('/', [ItemsController::class, 'store']);          // Create a new item
    Route::put('/{id}', [ItemsController::class, 'update']);      // Update an item
    Route::delete('/{id}', [ItemsController::class, 'destroy']);  // Delete an item by ID
    Route::delete('/po/{po_number}', [ItemsController::class, 'destroyItemsByPO']);  // Delete items by PO number

});


