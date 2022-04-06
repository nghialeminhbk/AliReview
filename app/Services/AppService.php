<?php

namespace App\Services;
use App\Models\App;
use Secomapp\ClientApi;
use Secomapp\Exceptions\ShopifyApiException;
use Secomapp\Resources\Product;
use App\Services\ServiceImp\CrawlAliReview;
use App\Services\ServiceImp\CrawlFeraReview;
use App\Services\ServiceImp\CrawlAutomizelyReview;
use App\Services\ServiceImp\CrawlJudgeReview;
use App\Services\ServiceImp\CrawlLooxReview;
use App\Services\ServiceImp\CrawlLaiReview;
use App\Services\ServiceImp\CrawlRivyoReview;
use App\Services\ServiceImp\CrawlRyviuReview;
use App\Services\ServiceImp\CrawlYotpoReview;
use App\Services\ServiceImp\CrawlStampedReview;
use App\Services\ServiceImp\CrawlShopifyReview;

class AppService
{
    protected CrawlService $crawlService;

    public function getAll(){
        return App::withCount('products')->get();
    }

    public function getAppById($appId){
        return App::find($appId);
    }

    public function add(array $data){
        $app = new App();
        $app->shop_name = $data['shopName'];
        $app->access_token = $data['accessToken'];
        $app->save();
        return $app->id;
    }

    public function delete($appId) : bool{
        $app = App::find($appId);
        $app->delete();
        return true;
    }

    public function getInstalledApps($shopName, $accessToken) : array{
        $result = [];
        $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        $productApi = new Product($client);
        try {
            $productRandom = $productApi->all([
                'limit' => '1'
            ])[0];
        } catch (ShopifyApiException $e) {
        }
        $urlProductDefault = "https://".$shopName.".myshopify.com/products/".$productRandom->handle;

        $apps = [
            'ali' => new CrawlAliReview(),
            'fera' => new CrawlFeraReview(),
            'automizely' => new CrawlAutomizelyReview(),
            'judge' => new CrawlJudgeReview(),
            'lai' => new CrawlLaiReview(),
            'loox' => new CrawlLooxReview(),
            'rivyo' => new CrawlRivyoReview(),
            'ryviu' => new CrawlRyviuReview(),
            'stamped' => new CrawlStampedReview(),
            'yotpo' => new CrawlYotpoReview(),
            'shopify' => new CrawlShopifyReview()
        ];
        foreach($apps as $appText => $instance){
            $this->crawlService = $instance;
            if($this->crawlService->checkAppInstalled($urlProductDefault)) $result[] = $appText;
        }
        return $result;
    }

}
