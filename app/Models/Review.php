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
        'title',
        'content',
        'img',
        'created_at',
        'store_reply',
        'store_created_at',
        'number_like',
        'number_dislike'
    ];

    protected $casts = [
        'img' => 'array',
        'store_reply' => 'array',
        'store_reply_created_at' => 'array'
    ];


}