<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Secomapp\ClientApi;
use Secomapp\Resources\Product;
use App\Services\CrawService;
use App\Services\AppService;
use App\Services\ProductService;
use App\Services\ReviewService;
use Secomapp\Exceptions\ShopifyApiException;
use App\Services\ServiceImp\CrawAliReview;
use App\Services\ServiceImp\CrawlLooxReview;
use App\Services\ServiceImp\CrawlJudgeReview;
use App\Services\ServiceImp\CrawlFeraReview;
use App\Services\ServiceImp\CrawlRyviuReview;
use App\Services\ServiceImp\CrawlStampedReview;
use App\Services\ServiceImp\CrawlRivyoReview;

class CrawController extends Controller
{
    protected CrawService $crawService;
    protected AppService $appService;
    protected ProductService $productService;
    protected ReviewService $reviewService;

    // public function __construct(AppService $appService, ProductService $productService, ReviewService $reviewService){
    //     $this->appService = $appService;
    //     $this->productService = $productService;
    //     $this->reviewService = $reviewService;
    // }

    public function index(){
        return view('index');
    }

    public function craw(Request $request){
        ini_set('max_execution_time', 1800);
        $this->appService = new AppService();
        $this->productService = new ProductService();
        $this->reviewService = new ReviewService();

        $shopName = $request->shopName;
        $accessToken = $request->accessToken;  

        $this->crawService = new CrawAliReview(); 
        if(!$this->checkValidToken($shopName, $accessToken)){
            return response()->json([
                'type' => 'error',
                'message' => 'Shop name or Token invalid!'
            ]);
        }

        if(!$this->crawService->checkAliReviewsInstalled($shopName, $accessToken)){
            return response()->json([
                'type' => 'error',
                'message' => 'Shop dont install AliReviews App or Shop has no products!'
            ]);
        }

        $appId = $this->appService->add([
            'shopName' => $shopName,
            'accessToken' => $accessToken
        ]);

        $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        $productApi = new Product($client);
        $markProductId = 0;
        while(1){
            try{
                $row = $productApi->all([
                    'limit' => 250,
                    'since_id' => $markProductId
                ]);
            }catch(ShopifyApiException $e){
                return response()->json([
                    'type' => 'error',
                    'message' => $e->getMessage()
                ]);
            }

            if(count($row) == 0) break;
            $markProductId = $row[count($row)-1]->id;
            
            foreach($row as $product){
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
                $this->crawService->crawData($urlProduct, $productId);
            }

            sleep(0.5);
        }

        return response()->json([
            'type' => 'success',
            'message' => "Craw data success!"
        ]);
        
    }

    public function checkValidToken($shopName, $accessToken){
        $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        $productApi = new Product($client);
        try{
            $products = $productApi->all([
                'limit' => 1
            ]);
        }catch(ShopifyApiException $e){
            return false;
        }

        return true;
    }

    public function test(){
        $crawlFeraReview = new CrawlFeraReview();
        // $crawlStampedReview->crawData("https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry", 0); return;

        // dump($crawlRivyoReview->crawData("https://thimatic-product-review.myshopify.com/products/red-t-shirt", 1)); return;
        // // dump($crawlRivyoReview->getProductIdOnStoreInstalledRivyo("https://thimatic-product-review.myshopify.com/products/red-t-shirt")); return;     
        // $handleString = function($string){
        //     $array = explode("\"", $string);
        //     return $array[strpos($string, "\"")+1];
        // };
        // $array = explode(" ",$string);
        // foreach($array as $i => $item){
        //     if($item == "store_pk:"){
        //         $storePk = $handleString($array[$i+1]);
        //         break;
        //     }
        // }
        // foreach($array as $i => $item){
        //     if($item == "product_id:"){
        //         $productId = $handleString($array[$i+1]);
        //         break;
        //     }
        // }
        // dump($array, $storePk, $productId); return;
        dump($crawlFeraReview->getUrlWidgetFeraReviews("https://rv-test-1.myshopify.com/products/abstract-v-back-cami?fera_admin=1")); return;
    }
}
