<?php

namespace App\Models;

use App\Models\Gig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GigFile extends Model
{
    use HasFactory;

    protected $fillable = [
        "gig_id",
        "media_url",
    ];

    public function gig(){
        return $this->belongsTo(Gig::class);
    }
}
