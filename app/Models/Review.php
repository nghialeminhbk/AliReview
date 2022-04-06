<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $product_id
 * @property mixed $rate
 * @property mixed $author_name
 * @property mixed $author_avt
 * @property mixed $title
 * @property mixed $content
 * @property mixed $img
 * @property mixed $created_at
 * @property mixed $store_reply
 * @property mixed $store_reply_created
 * @property mixed $number_like
 * @property mixed $number_dislike
 */
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
