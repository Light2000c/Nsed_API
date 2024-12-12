<?php

namespace App\Models;

use App\Models\Gig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GigPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        "gig_id",
        "package_name",
        "description",
        "price",
        "delivery_time",
        "revision_limit",
    ];

    public function gig(){
        return $this->belongsTo(Gig::class);
    }
}
