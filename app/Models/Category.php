<?php

namespace App\Models;

use App\Models\Gig;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public $fillable = [
        "name"
    ];

    public function gig(){
        return $this->hasMany(Gig::class);
    }
}
