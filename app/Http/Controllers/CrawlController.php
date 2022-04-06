<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Secomapp\ClientApi;
use Secomapp\Resources\Product;
use App\Services\CrawlService;
use App\Services\AppService;
use App\Services\ProductService;
use App\Services\ReviewService;
use Secomapp\Exceptions\ShopifyApiException;
use App\Services\ServiceImp\CrawlAliReview;
use App\Services\ServiceImp\CrawlRivyoReview;

class CrawlController extends Controller
{
    protected CrawlService $crawlService;
    protected AppService $appService;
    protected ProductService $productService;
    protected ReviewService $reviewService;

    public function __construct(AppService $appService, ProductService $productService, ReviewService $reviewService){
        $this->appService = $appService;
        $this->productService = $productService;
        $this->reviewService = $reviewService;
    }

    public function index(){
        return view('index');
    }

    public function crawl(Request $request){
        ini_set('max_execution_time', 1800);

        $shopName = $request->input('shopName');
        $accessToken = $request->input('accessToken');

        $this->crawlService = new CrawlAliReview();
        if(!$this->checkValidToken($shopName, $accessToken)){
            return response()->json([
                'type' => 'error',
                'message' => 'Shop name or Token invalid!'
            ]);
        }

        if(!$this->crawlService->checkAppInstalled("13231")){
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
                $this->crawlService->crawlData($urlProduct,"12", $productId);
            }

            sleep(0.5);
        }

        return response()->json([
            'type' => 'success',
            'message' => "Craw data success!"
        ]);

    }

    public function checkValidToken($shopName, $accessToken) : bool
    {
        $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        $productApi = new Product($client);
        try{
            $productApi->all([
                'limit' => 1
            ]);
        }catch(ShopifyApiException $e){
            return false;
        }

        return true;
    }

    public function test(){
        $this->crawlService = new CrawlAliReview();
        // dump($this->crawlService->crawlData("https://smartify-reviews-app-demo.myshopify.com/products/sample-product", "6868040188112", 0)); return;

        // dump($this->crawlService->checkAppInstalled("https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry")); return;

//         dump($this->appService->getInstalledApps('rv-test-1', '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'));
        // dump($this->crawlService->getApiGetProductsStamped("https://rv-test-1.myshopify.com/products/aachoae-women-elegant-long-wool-coat-with-belt-solid-color-long-sleeve-chic-outerwear-ladies-drop-shoulder-overcoat-2021", "6807065755692")); return;
        // dump($this->crawlService->crawlData("https://rv-test-1.myshopify.com/products/aachoae-women-elegant-long-wool-coat-with-belt-solid-color-long-sleeve-chic-outerwear-ladies-drop-shoulder-overcoat-2021", "6807065755692", "1")); return;
        // dump($this->crawlService->crawlData("https://rv-test-1.myshopify.com/products/chic-women-trench-coat-casual-women-39-s-long-outerwear-loose-overcoat-autumn-winter-fashion-double-breasted-windbreaker-femme", "6807065690156", "1")); return;
//        dump($this->crawlService->crawlData("https://rv-test-1.myshopify.com/products/chic-women-trench-coat-casual-women-39-s-long-outerwear-loose-overcoat-autumn-winter-fashion-double-breasted-windbreaker-femme", "6807065690156", "1"));

        $this->crawlService->checkAppInstalled("https://ali-reviews-fireapps.myshopify.com/products/charmsmic-new-striped");
    }
}
