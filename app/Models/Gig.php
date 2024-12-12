<?php

namespace App\Models;

use App\Models\Seller;
use App\Models\GigFile;
use App\Models\Category;
use App\Models\GigReview;
use App\Models\GigGallery;
use App\Models\GigPackage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gig extends Model
{
    use HasFactory;

    protected $fillable = [
        "seller_id",
        "category_id",
        "title",
        "description",
        "status",
        "views_count",
        "orders_count",
        "terms_of_service",
    ];


    public function seller(){
        return $this->belongsTo(Seller::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }


    public function package() {
        return $this->hasMany(GigPackage::class);
    }

    public function file() {
        return $this->hasMany(GigFile::class);
    }

    public function review() {
        return $this->hasMany(GigReview::class);
    }
}
