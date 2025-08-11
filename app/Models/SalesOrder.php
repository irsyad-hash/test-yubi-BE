<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'po_buyer_no',
        'order_type_id',
        'order_date',
        'shipping_date',
        'customer_id',
        'currency_id',
        'email',
        'phone',
        'exchange_rate',
        'pph',
        'status_id',
        'vat',
        'buyer_address',
        'sub_amount',
        'total_discount',
        'after_discount',
        'total_vat',
        'total_pph',
        'grand_total',
    ];
    
    public function details()
{
    return $this->hasMany(SalesOrderDetail::class);
}
}

