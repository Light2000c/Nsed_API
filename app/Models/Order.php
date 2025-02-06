<?php

namespace App\Models;

use App\Models\Gig;
use App\Models\Buyer;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        "buyer_id",
        "seller_id",
        "gig_id",
        "quantity",
        "total_price",
        "status",
        "payment_status",
        "modification_request",
        "transaction_reference",
        "order_date",
        "delivery_date",
    ];

    public function buyer(){
        return $this->belongsTo(Buyer::class);
    }

    public function seller(){
        return $this->belongsTo(Seller::class);
    }

    public function gig(){
        return $this->belongsTo(Gig::class);
    }
}
