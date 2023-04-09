<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_Image extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'product__images';
    protected $guarded = [];
}
