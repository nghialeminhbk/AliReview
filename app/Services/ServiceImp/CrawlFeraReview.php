<?php
namespace App\Services\ServiceImp;
use App\Services\CrawService;
use GuzzleHttp\Client;
use Secomapp\ClientApi;
use Secomapp\Resources\Product;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;
use Secomapp\Exceptions\ShopifyApiException;
use App\Services\ReviewService;

class CrawlFeraReview implements CrawService
{
    public function crawData($url, $productId){
        $urlWidget = $this->getUrlWidgetFeraReviews($url);
        
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

    public function getUrlWidgetFeraReviews($url){
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(RequestException $e){
            return null;
        }
        $html = strip_tags((string) $response->getBody(), ["<script>"]);
        
        // crawler
        $crawler = new Crawler($html);
        try{
            // $string = $crawler->filter('script')->last()->text();
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
            foreach($array as $i => $item){
                if($item == "product_id:"){
                    $productId = $handleString($array[$i+1]);
                    break;
                }
            }
            $productId = "4820800241708";
            $storePk = "pk_63aba574a2017d3ddbfa0e0d592e8c8e637174d531e87bdbd0667ceaaf618c53";
        }catch(\InvalidArgumentException $e){
            return null;
        }

        return "https://api2.fera.ai/public/reviews.json?limit=5&sort_by=highest_quality&product_id=".$productId."&public_key=".$storePk."&admin=true&api_client=fera.js-2.6.4.&page=";

    }

    public function checkStoreInstalledFeraReview($shopName){
        $client = new Client();
        $response = $client->get("https://".$shopName.".myshopify.com/collections/all");
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);


        try{
            if(strpos($crawler->filter('script')->last()->text(), "fera") >= 0){
                return true;
            }
        }catch(\InvalidArgumentException $e){
            return false;
        }
        return false;
    }
}