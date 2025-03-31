<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class items extends Model
{
    //
    use HasFactory;

    protected $table ='tbl_items';

    protected $fillable =[
        'po_no',
        'brand_name',
        'generic_name',
        'dosage_form',
        'dosage',
        'category',
        'unit',
        'price',
        'quantity',
        'price',
        'expiration_date',
        'user_id',
    ];


    protected static function booted()
    {
        static::created(function ($item) {
            // Create daily inventory record when new stock is added
            daily_inventory::create([
                'stock_id' => $item->id,
                'Openning_quantity' => $item->quantity,
                'Closing_quantity' => $item->quantity,
                'quantity_out' => 0, // Initial value for new stock
                'transaction_date' => now()->format('Y-m-d'),
                'remarks' => 'Initial stock entry',
                'status' => 'OPEN',
                'user_id' => $item->user_id
            ]);
        });


    }

}
