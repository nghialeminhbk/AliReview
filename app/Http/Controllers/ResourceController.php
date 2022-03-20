<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppService;
use App\Services\ProductService;
use App\Services\ReviewService;

class ResourceController extends Controller
{
    protected AppService $appService;
    protected ProductService $productService;
    protected ReviewService $reviewService;

    public function __construct(AppService $appService, ProductService $productService, ReviewService $reviewService){
        $this->appService = $appService;
        $this->productService = $productService;
        $this->reviewService = $reviewService;
    }

    public function displayListApps(){
        $apps = $this->appService->getAll();
        return view('sections.listApps', [
            'data' => [
                'apps' => $apps
            ]
        ]);
    }

    public function displayListProducts($appId){
        $products = $this->productService->getAllProductsByAppId($appId);
        $app = $this->appService->getAppById($appId);
        return view('sections.listProducts', [
            'data' => [
                'products' => $products,
                'appName'  => $app->shop_name
            ]
        ]);
    }

    public function displayListReviews($productId){
        $reviews = $this->reviewService->getReviewsByProductId($productId);
        $product = $this->productService->getProductById($productId);
        return view('sections.listReviews', [
            'data' => [
                'reviews' => $reviews,
                'productTitle' => $product->title,
                'appId' => $product->app_id 
            ]
        ]);
    }

}
