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

class CrawlLaiReview implements CrawlService
{
    public function crawlData($urlProduct, $productIdOriginal, $productId){
        $shopName = substr($urlProduct, 8, strpos($urlProduct, ".myshopify.com")-8);
        $apiReviews = "https://reviews.smartifyapps.com/api/load-more?productShopifyId=".$productIdOriginal."&shopName=".$shopName."&reviewPerPage=10&sortValue=date&rate=null&page=";

        $client = new Client();
        $currentPage = 1;
        $laiReviews = [];
        while(1){
            $response = $client->get($apiReviews.$currentPage);
            $data = json_decode(base64_decode(json_decode((string) $response->getBody())->blockReviews));

            if(count($data) == 0) break;

            foreach($data as $review){
                // save review to db
                $temp['rate'] = $review->rating;
                $temp['authorName'] = $review->author;
                $temp['authorAvt'] = null;
                $temp['content'] = $review->review;
                $temp['title'] = null;
                $temp['img'] = count($review->photosArray) > 0?$review->photosArray:null;
                $temp['createdAt'] = $review->date;
                $temp['storeReply'] = property_exists($review, 'reply')?array_map(function($item){
                    return $item->content;
                }, $review->reply):null;
                $temp['storeReplyCreated'] = property_exists($review, 'reply')?array_map(function($item){
                    return $item->created_at;
                }, $review->reply):null;
                $temp['numberLike'] = $review->likes??0;
                $temp['numberDislike'] = $review->dislikes??0;

                array_push($laiReviews, $temp);
            }

            $currentPage++;

            sleep(0.5);
        }
        dump($laiReviews);
        return count($laiReviews);
    }

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault); 
        }catch(RequestException $e){
            return false;
        }
        $string = (string) $response->getBody();
       
        if(strpos($string, "https://reviews.smartifyapps.com/api/load-more")){
            return true;
        }

        return false;
    }
}