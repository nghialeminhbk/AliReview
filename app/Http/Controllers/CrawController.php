<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Secomapp\ClientApi;
use Secomapp\Resources\Product;
use App\Services\CrawService;
use App\Services\ServiceImp\CrawAliReview;
use App\Services\AppService;
use App\Services\ProductService;
use App\Services\ReviewService;
use Secomapp\Exceptions\ShopifyApiException;

class CrawController extends Controller
{
    protected CrawService $crawService;
    protected AppService $appService;
    protected ProductService $productService;
    protected ReviewService $reviewService;

    public function __construct(AppService $appService, ProductService $productService, ReviewService $reviewService){
        $this->appService = $appService;
        $this->productService = $productService;
        $this->reviewService = $reviewService;
    }

    public function index(){
        // ini_set('max_execution_time', 300);
        // $shopName = "rv-test-1";
        // $accessToken = "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";  

        // $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        // $productApi = new Product($client);

        // try{
        //     $products = $productApi->all();
        // }catch(ShopifyApiException $e){
        //     dump("error authorize"); return;
        //     return response()->json([
        //         'data' => [
        //             'message' => $e->getMessage()
        //         ]
        //     ]);
        // }

        // dump($products); return;

        // $appId = $this->appService->add([
        //     'shopName' => $shopName,
        //     'accessToken' => $accessToken
        // ]);

        // $this->crawService = new CrawAliReview();
        // foreach($products as $product){
        //     $productId = $this->productService->add([
        //         'appId' => $appId,
        //         'title' => $product->title,
        //         'vendor' => $product->vendor,
        //         'productType' => $product->product_type,
        //         'status' => $product->status,
        //         'tags' => $product->tags,
        //         'handle' => $product->handle
        //     ]);

        //     $urlProduct = 'https://'.$shopName.'.myshopify.com/products/'.$product->handle;
        //     $reviews = $this->crawService->crawData($urlProduct);
            
        //     foreach($reviews as $review){
        //         $this->reviewService->add([
        //             'productId' => $productId,
        //             'rate' => $review['rate'],
        //             'authorName' => $review['author']['name'],
        //             'authorAvt' => $review['author']['avt'],
        //             'content' => $review['content'],
        //             'img' => $review['img'],
        //             'date' => $review['date'],
        //             'numberLike' => $review['number_like'],
        //             'numberUnlike' => $review['number_unlike']
        //         ]);
        //     }
        // }
        // dump("success roi nha!"); return;
        // return response()->json([
        //     'data' => [
        //         'message' => "Craw data success!"
        //     ]
        // ]);

        return view('index');
    }

    public function craw(Request $request){
        ini_set('max_execution_time', 1800);
        $shopName = $request->shopName;
        $accessToken = $request->accessToken;  

        $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        $productApi = new Product($client);

        try{
            $products = $productApi->all();
        }catch(ShopifyApiException $e){
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        $appId = $this->appService->add([
            'shopName' => $shopName,
            'accessToken' => $accessToken
        ]);

        $this->crawService = new CrawAliReview();
        foreach($products as $product){
            $productId = $this->productService->add([
                'appId' => $appId,
                'title' => $product->title,
                'vendor' => $product->vendor,
                'productType' => $product->product_type,
                'status' => $product->status,
                'tags' => $product->tags,
                'handle' => $product->handle
            ]);

            $urlProduct = 'https://'.$shopName.'.myshopify.com/products/'.$product->handle;
            $reviews = $this->crawService->crawData($urlProduct);
            foreach($reviews as $review){
                $this->reviewService->add([
                    'productId' => $productId,
                    'rate' => $review['rate'],
                    'authorName' => $review['author']['name'],
                    'authorAvt' => $review['author']['avt'],
                    'content' => $review['content'],
                    'img' => $review['img'],
                    'date' => $review['date'],
                    'numberLike' => $review['number_like'],
                    'numberUnlike' => $review['number_unlike']
                ]);
            }
        }

        return response()->json([
            'type' => 'success',
            'message' => "Craw data success!"
        ]);
        
    }
}
