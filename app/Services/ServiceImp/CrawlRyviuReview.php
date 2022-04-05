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

class CrawlRyviuReview implements CrawlService
{
    public function crawlData($urlProduct, $productIdOriginal, $productId){
        $shopDomain = substr($urlProduct, 8, strpos($urlProduct, '/products')-8);
        $apiGetReviews = "https://app.ryviu.io/frontend/client/get-more-reviews?domain=".$shopDomain;
        $currentPage = 1;
        $ryviuReviews = [];
        
        $client = new Client();

        while(1){
            $header = [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    "product_id" => $productIdOriginal,
                    "page" => $currentPage,
                    "domain" => $shopDomain,
                    "platform" => "shopify"
                ])
            ];
            $response = $client->post($apiGetReviews, $header);
            $data = json_decode((string) $response->getBody())->more_reviews;
            
            if(count($data) == 0) break;

            foreach($data as $review){
                // save review to db
                $temp['rate'] = $review->rating;
                $temp['authorName'] = $review->author;
                $temp['authorAvt'] = $review->avatar??null;
                $temp['content'] = $review->body_text;
                $temp['title'] = $review->title;
                $temp['img'] = $review->body_urls??null;
                $temp['createdAt'] = $review->created_at;
                $temp['storeReply'] = $review->replys;
                $temp['numberLike'] = $review->like;
                $temp['numberDislike'] = $review->dislike;

                array_push($ryviuReviews, $temp);
            }

            $currentPage++;

            sleep(0.5);
        }
        // dump($ryviuReviews);
        return count($ryviuReviews);
    }

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(RequestException $e){
            return false;
        }
        $string = (string) $response->getBody();

       if(strpos($string, "cdn.ryviu.com")) return true;

        return false;
    }
}