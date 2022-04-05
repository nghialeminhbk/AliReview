<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use Secomapp\ClientApi;
use Secomapp\Resources\Product;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;
use Secomapp\Exceptions\ShopifyApiException;
use App\Services\ReviewService;

class CrawlYotpoReview implements CrawlService
{
    public function crawlData($urlProduct,$productIdOriginal, $productId){
        $apiWidget = $this->apiYoptoWidgetReview($urlProduct);

        if(is_null($apiWidget)) return false;

        $currentPage = 1;
        $yotpoReviews = [];
        while(1){
            $param = [
                'methods' => json_encode([
                    [
                        "method" => "reviews",
                        "params" => [
                            "pid" => $productIdOriginal,
                            "order_metadata_fields" => [],
                            "widget_product_id" => $productIdOriginal,
                            "data_source" => "default",
                            "page" => $currentPage,
                            "host-widget" => "main_widget"]
                        ]])
            ];
            $response = $client->get($apiWidget, [
                'query' => $param
            ]);
            $html = json_decode((string) $response->getBody())->result;
            $crawler = new Crawler($html);
            $row = $crawler
            ->filter('.yotpo-review')
            ->reduce(function(Crawler $node){
                return ($node->attr('data-review-id') > 0);
            })
            ->each(function( Crawler $node){
                    return [
                        'img' => null,
                        'authorName' => $node->filter('.yotpo-user-name')->text(),
                        'authorAvt' => null,
                        'createdAt' => $node->filter('.yotpo-header .yotpo-review-date')->text(),
                        'rate' => count($node->filter('.yotpo-icon-star')),
                        'title' => $node->filter('.yotpo-main .content-title')->text(),
                        'content' => $node->filter('.yotpo-main .yotpo-review-wrapper .content-review')->text(),
                        'storeReply' => count($node->filter('.yotpo-comments-box'))>0?$node->filter('.yotpo-comments-box .yotpo-main .content-review')->text():null,
                        'storeReplyCreated' => count($node->filter('.yotpo-comments-box'))>0?$node->filter('.yotpo-comments-box .yotpo-header .yotpo-review-date')->text():null,
                        'numberLike' => $node->filter('.vote-sum')->first()->text(),
                        'numberDislike' => $node->filter('.vote-sum')->last()->text()
                    ];
            });

            if(count($row) == 0) break;

            $yotpoReviews = array_merge($yotpoReviews, $row);

            $currentPage++;

        }
        // dump($yotpoReviews);
        return true;
    }

    public function apiYoptoWidgetReview($url){
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(RequestException $e){
            return null;
        }
        
        $htmlString = (string) $response->getBody();

        // crawler
        $crawler = new Crawler($htmlString);
        try{
            $appKey = "";
            $string = $crawler->filter('#shopify-block-7570552984445421165')->first()->text();
            $array = explode("/", $string);
            foreach($array as $i => $item){
                if($item == "staticw2.yotpo.com"){
                    $appKey = $array[$i+1];
                    break;
                }
            }
        }catch(\InvalidArgumentException $e){
            return null;
        }
        
        return "https://staticw2.yotpo.com/batch/app_key/".$appKey."/yotpo_site_reviews";
    } 

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(RequestException $e){
            return false;
        }
        $string = (string) $response->getBody();

        if(strpos($string, "staticw2.yotpo.com")){
            return true;
        }

        return false;
    }
}