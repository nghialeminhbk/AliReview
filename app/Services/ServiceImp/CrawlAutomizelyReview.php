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

class CrawlAutomizelyReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId){
        $shopName = substr($urlProduct, 8, strpos('.myshopify.com')-8);

        $apiReviews = "https://api.automizely.com/reviews/v1/shopper/reviews?store_key=".$shopName."&product_external_id=".$originalProductId."&limit=5&page=";

        $currentPage = 1;
        $automizelyReviews = [];
        while(1){
            $response = $client->get($apiReviews);
            $data = json_decode((string) $response->getBody())->data->reviews;

            if(count($data) == 0) break;
            
            foreach($data as $review){
                 // save review to db
                 $temp['rate'] = $review->star;
                 $temp['authorName'] = $review->author->name;
                 $temp['authorAvt'] = $review->author->avatar_url;
                 $temp['content'] = $review->content;
                 $temp['title'] = null;
                 $temp['img'] = $review->images_urls;
                 $temp['createdAt'] = $review->commented_at;
                 $temp['storeReply'] = $review->merchant_reply->content;
                 $temp['storeReplyCreated'] = $review->merchant_reply->created_at;
                 $temp['numberLike'] = null;
                 $temp['numberDislike'] = null;
 
                 array_push($automizelyReviews, $temp);
            }

            if(count($row) == 0) break;

            $automizelyReviews = array_merge($automizelyReviews, $row);

            $currentPage++;

            sleep(0.5);
        }
      
        return true;
    }

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(RequestException $e){
            return false;
        }
        $html = strip_tags((string) $response->getBody(), ["<script>"]);
        if(strpos($html, "widgets.automizely.com\/reviews")){
            return true;
        }


        return false;
    }
}