<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    protected $fillable = [
        'sales_order_id',
        'ref_type_id',
        'ref_num',
        'item_type_id',
        'product_code',
        'product_name',
        'unit_type_id',
        'price',
        'quantity',
        'discount_amount',
        'discount_percent',
        'total_amount',
        'remark',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
