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

class CrawlRyviuReview implements CrawService
{
    public function crawData($url, $productId){
        $shopDomain = substr($url, 8, strpos($url, '/products')-8);
        $apiGetReviews = "https://app.ryviu.io/frontend/client/get-more-reviews?domain=".$shopDomain;
        $productId = $this->getProductIdOnStoreInstalledRyviu($url);
        $currentPage = 1;
        $ryviuReviews = [];
        
        $client = new Client();

        while(1){
            $header = [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    "product_id" => $productId,
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

    public function getProductIdOnStoreInstalledRyviu($urlProduct){
        $client = new Client();
        $response = $client->get($urlProduct);
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);
        $productId = $crawler->filter('ryviu-widget-total')->attr('product_id');
        return $productId;
    }

    public function checkStoreInstalledRyviuReview($shopName){
        $client = new Client();
        $response = $client->get("https://".$shopName.".myshopify.com/collections/all");
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        if(count($crawler->filter('ryviu-widget-total')) > 0){
            return true;
        }

        return false;
    }
}