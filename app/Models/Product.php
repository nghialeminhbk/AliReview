<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $title
 * @property mixed $app_id
 * @property mixed $vendor
 * @property mixed $product_type
 * @property mixed $status
 * @property mixed $tags
 * @property mixed $handle
 * @property mixed $id
 * @method static find($productId)
 * @method static withCount(string $string)
 * @method static where(string $string, $appId)
 */
class Product extends Model
{
    use HasFactory;

    protected $table = "products";
    public $timestamps = false;

    public function reviews(){
        return $this->hasMany(Review::class);
    }
}
