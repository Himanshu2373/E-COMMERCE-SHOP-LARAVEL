<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;

class category extends Model
{
    use HasFactory;

    public function sub_category(){
        return $this->hasMany(SubCategory::class);
    }
}
