<?php

namespace App\Models;

use App\Models\Gig;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "display_name",
        "bio",
        "category",
        "rating",
        "tottal_gigs",
        "earnings"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gig()
    {
        return $this->hasMany(Gig::class);
    }

    public function ownsGigPackage($gigPackage)
    {
        return $this->is($gigPackage->gig->seller);
    }
}
