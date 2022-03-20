<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = "reviews";
    public $timestamps = false;
    
    protected $fillable = [
        'product_id',
        'rate',
        'author_name',
        'author_avt',
        'content',
        'img',
        'date',
        'number_like',
        'number_unlike'
    ];

}