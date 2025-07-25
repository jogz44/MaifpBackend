<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
//         DB::statement("
//         CREATE OR REPLACE VIEW vw_stock_card AS
//         SELECT
//     di.transaction_date AS date,
// 	i.po_no,
//     i.generic_name,
//     i.brand_name,

//     CASE
//         WHEN dt.id IS NOT NULL THEN 'OUT'
//         WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN 'IN'
//         ELSE 'OPENNING'
//     END AS transaction_type,

//     CASE
//         WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN di.Openning_quantity
//         ELSE 0
//     END AS quantity_in,

//     COALESCE(dt.quantity, 0) AS quantity_out,

//     di.Openning_quantity,
//     di.Closing_quantity,

//     COALESCE( di.remarks) AS remarks


// FROM tbl_daily_inventory di

// JOIN tbl_items i ON i.id = di.stock_id

// LEFT JOIN tbl_daily_transactions dt
//     ON dt.item_id = di.stock_id AND dt.transaction_date = di.transaction_date



// ORDER BY di.transaction_date, dt.id;
// ");

// DB::statement("
//         CREATE OR REPLACE VIEW vw_stock_card AS
//         SELECT
//     di.transaction_date AS date,
//     i.po_no,
//     i.generic_name,
//     i.brand_name,

//     CASE
//         WHEN dt.id IS NOT NULL THEN 'OUT'
//         WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN 'IN'
//         ELSE 'OPENING'
//     END AS transaction_type,

//     CASE
//         WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN di.Openning_quantity
//         ELSE 0
//     END AS quantity_in,

//     COALESCE(dt.quantity, 0) AS quantity_out,

//     di.Openning_quantity,
//     di.Closing_quantity,

//     COALESCE(di.remarks, '') AS remarks

// FROM tbl_daily_inventory di

// JOIN tbl_items i ON i.id = di.stock_id

// LEFT JOIN tbl_daily_transactions dt
//     ON dt.item_id = di.stock_id AND dt.transaction_date = di.transaction_date

//     where generic_name='Paracetamol' and brand_name='Biogesic'

// ORDER BY i.generic_name, di.transaction_date, dt.id;");

// DB::statement("
//         CREATE OR REPLACE VIEW vw_stock_card AS
//         SELECT
//     stock_card.*,

//     -- Running balance per generic_name, ordered by date
//     SUM(quantity_in - quantity_out) OVER (
//         PARTITION BY generic_name
//         ORDER BY date, inventory_id, transaction_id
//         ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
//     ) AS running_balance

// FROM (
//     SELECT
//         di.id AS inventory_id,
//         dt.id AS transaction_id,
//         di.transaction_date AS date,
//         i.generic_name,
//         i.brand_name,
//         i.po_no,

//         CASE
//             WHEN dt.id IS NOT NULL THEN 'OUT'
//             WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN 'IN'
//             ELSE 'OPENING'
//         END AS transaction_type,

//         CASE
//             WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN di.Openning_quantity
//             ELSE 0
//         END AS quantity_in,

//         COALESCE(dt.quantity, 0) AS quantity_out,

//         di.Openning_quantity,
//         di.Closing_quantity,
//         COALESCE(di.remarks, '') AS remarks

//     FROM tbl_daily_inventory di

//     JOIN tbl_items i ON i.id = di.stock_id

//     LEFT JOIN tbl_daily_transactions dt
//         ON dt.item_id = di.stock_id AND dt.transaction_date = di.transaction_date
// ) AS stock_card


// ORDER BY generic_name, date, inventory_id;

//        ");

       DB::statement("
        CREATE OR REPLACE VIEW vw_stock_card AS
       SELECT
    stock_card.*,

    -- Running balance per generic_name and brand_name, ordered by date
    SUM(quantity_in - quantity_out) OVER (
        PARTITION BY generic_name, brand_name
        ORDER BY date
        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW
    ) AS running_balance

FROM (
    SELECT
        di.transaction_date AS date,
        i.generic_name,
        i.brand_name,

        -- Determine transaction type
        CASE
            WHEN SUM(CASE WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN 1 ELSE 0 END) > 0 THEN 'IN'
            WHEN SUM(COALESCE(dt.total_quantity, 0)) > 0 THEN 'OUT'
            ELSE 'OPENING'
        END AS transaction_type,

        -- Quantity in (initial stock entry only)
        SUM(CASE
            WHEN LOWER(di.remarks) LIKE '%initial stock entry%' THEN di.Openning_quantity
            ELSE 0
        END) AS quantity_in,

        -- Quantity out (aggregated for compatibility with ONLY_FULL_GROUP_BY)
        SUM(COALESCE(dt.total_quantity, 0)) AS quantity_out,

        -- Prevent over-counting by using MIN/MAX
        MIN(di.Openning_quantity) AS openning,
        MAX(di.Closing_quantity) AS closing,

        GROUP_CONCAT(DISTINCT COALESCE(di.remarks, ' ') SEPARATOR '; ') AS remarks

    FROM tbl_daily_inventory di

    JOIN tbl_items i ON i.id = di.stock_id

    LEFT JOIN (
        -- Sum total quantity per item per day
        SELECT
            item_id,
            transaction_date,
            SUM(quantity) AS total_quantity
        FROM tbl_daily_transactions
        GROUP BY item_id, transaction_date
    ) dt ON dt.item_id = di.stock_id AND dt.transaction_date = di.transaction_date

    GROUP BY di.transaction_date, i.generic_name, i.brand_name
) AS stock_card
 
ORDER BY generic_name, brand_name, date;



       ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement("drop view if exists vw_stock_card;");
    }
};
