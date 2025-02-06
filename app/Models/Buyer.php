<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = [
        "total_purchases",
        "spending",
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function order(){
        return $this->hasMany(Order::class);
    }
}
