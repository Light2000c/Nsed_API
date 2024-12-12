<?php

namespace App\Models;

use App\Models\Gig;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GigReview extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "gig_id",
        "rating",
        "comment",
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function gig(){
        return $this->belongsTo(Gig::class);
    }
}
