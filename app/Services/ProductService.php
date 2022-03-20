<?php

namespace App\Services;
use App\Models\Product;

class ProductService 
{
    public function getAll(){
        return Product::withCount('reviews')->get();
    }

    public function getAllProductsByAppId($appId){
        return Product::where('app_id', $appId)->withCount('reviews')->get();
    }

    public function getProductById($id){
        return Product::find($id);
    }

    public function add(array $data){
        $product = new Product(); 
        $product->app_id = $data['appId'];
        $product->title = $data['title'];
        $product->vendor = $data['vendor'];
        $product->product_type = $data['productType'];
        $product->status = $data['status'];
        $product->tags = $data['tags'];
        $product->handle = $data['handle'];
        $product->save();

        return $product->id;
    }
    
    public function delete($productId) : bool{
        $product = Product::find($productId);
        $product->delete();
        return true;
    }

}