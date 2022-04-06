<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static withCount(string $string)
 * @method static find($appId)
 * @property mixed $shop_name
 * @property mixed $access_token
 * @property mixed $id
 */
class App extends Model
{
    use HasFactory;

    protected $table = "apps";
    public $timestamps = false;

    public function products(){
        return $this->hasMany(Product::class);
    }
}
