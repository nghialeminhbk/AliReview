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

class CrawlShopifyReview implements CrawlService
{
    public function crawlData($urlProduct, $productIdOriginal, $productId){
        $apiWidget = $this->apiShopifyReview($urlProduct, $productIdOriginal);

        if(is_null($apiWidget)) return false;

        $currentPage = 1;
        $shopifyReviews = [];
        while(1){
            $response = $client->get($apiWidget.$currentPage);
            $html = json_decode((string) $response->getBody())->reviews;
            $crawler = new Crawler($html);
            $row = $crawler
            ->filter('.spr-review')
            ->each(function( Crawler $node){
                    return [
                        'immg' => null,
                        'authorName' => $node->filter('.spr-review-header-byline strong')->first()->text(),
                        'authorAvt' => null,
                        'createdAt' => $node->filter('.spr-review-header-byline strong')->last()->text(),
                        'rate' => count($node->filter('.spr-icon-star')),
                        'title' => $node->filter('.spr-review-header-title')->text(),
                        'content' => $node->filter('.spr-review-content-body')->text(),
                        'storeReply' => count($node->filter('.spr-review-reply'))>0?$node->filter('.spr-review-reply')->text():null,
                        'storeReplyCreated' => null,
                        'numberLike' => null,
                        'numberDislike' => null
                    ];
            });

            if(count($row) == 0) break;

            $shopifyReviews = array_merge($shopifyReviews, $row);

            $currentPage++;

        }
        // dump($yotpoReviews);
        return true;
    }

    public function apiShopifyReview($urlProduct, $productIdOriginal){
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(RequestException $e){
            return null;
        }
        
        $htmlString = (string) $response->getBody();

        $shopDomain = substr($url, 8, strpos($urlProduct, '/products')-8);
        
        return "https://productreviews.shopifycdn.com/proxy/v4/reviews?product_id=".$productIdOriginal."&shop=".$shopDomain."&page=";
    } 

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(RequestException $e){
            return false;
        }
        $string = (string) $response->getBody();
        if(strpos($string, "productreviews.shopifycdn.com")){
            return true;
        }

        return false;
    }
}