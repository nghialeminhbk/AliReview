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

class CrawlFeraReview implements CrawlService
{
    public function crawlData($urlProduct, $productIdOriginal, $productId){
        $urlWidget = $this->getUrlWidgetFeraReviews($urlProduct, $productIdOriginal);
        
        if(is_null($urlWidget)) return false;
        
        $currentPage = 1;
        $feraReviews = [];
        $client = new Client();
        while(1){
            $response = $client->get($urlWidget.$currentPage);
            $data = json_decode((string) $response->getBody());

            if(count($data) == 0) break;

            foreach($data as $review){
                // save review to db
                $temp['rate'] = $review->rating;
                $temp['authorName'] = $review->customer_name;
                $temp['authorAvt'] = $review->customer_avatar_url;
                $temp['content'] = $review->body;
                $temp['title'] = $review->heading;
                $temp['img'] = $review->photos;
                $temp['createdAt'] = $review->created_at;
                $temp['storeReply'] = $review->store_reply;
                $temp['storeReplyCreated'] = $review->store_replied_at;
                $temp['numberLike'] = null;
                $temp['numberDislike'] = null;

                array_push($feraReviews, $temp);
            }

            $currentPage++;
        }
        return count($feraReviews);
    }

    public function getUrlWidgetFeraReviews($urlProduct, $productIdOriginal){
        $client = new Client();
        try{
            $response = $client->get($urlProduct);
        }catch(RequestException $e){
            return null;
        }
        $html = strip_tags((string) $response->getBody(), ["<script>"]);
        
        // crawler
        $crawler = new Crawler($html);
        try{
            $string = $crawler->filter('script')->last()->text();
            $handleString = function($string){
                $array = explode("\"", $string);
                return $array[strpos($string, "\"")+1];
            };
            $array = explode(" ",$string);
            foreach($array as $i => $item){
                if($item == "store_pk:"){
                    $storePk = $handleString($array[$i+1]);
                    break;
                }
            }
            $storePk = "pk_63aba574a2017d3ddbfa0e0d592e8c8e637174d531e87bdbd0667ceaaf618c53";
        }catch(\InvalidArgumentException $e){
            return null;
        }

        return "https://api2.fera.ai/public/reviews.json?limit=5&sort_by=highest_quality&product_id=".$productIdOriginal."&public_key=".$storePk."&admin=true&api_client=fera.js-2.6.4.&page=";

    }

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(RequestException $e){
            return false;
        }
        $string = (string) $response->getBody();
        if(strpos($string, "https://cdn.fera.ai/js/fera.js?shop=")){
            return true;
        }
        return false;
    }

}