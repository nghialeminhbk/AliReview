<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;



class CrawlRyviuReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId){
        $shopDomain = substr($urlProduct, 8, strpos($urlProduct, '/products')-8);
        $apiGetReviews = "https://app.ryviu.io/frontend/client/get-more-reviews?domain=".$shopDomain;
        $currentPage = 1;
        $ryviuReviews = [];

        $client = new Client();

        while(1){
            $header = [
                'headers' => ['Content-Type' => 'application/json'],
                'body' => json_encode([
                    "product_id" => $originalProductId,
                    "page" => $currentPage,
                    "domain" => $shopDomain,
                    "platform" => "shopify"
                ])
            ];
            try {
                $response = $client->post($apiGetReviews, $header);
                $data = json_decode((string) $response->getBody())->more_reviews;
            } catch (GuzzleException $e) {
                return false;
            }

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

                $ryviuReviews[] = $temp;
            }

            $currentPage++;

            sleep(0.5);
        }
        // dump($ryviuReviews);
        return true;
    }

    public function checkAppInstalled($urlProductDefault): bool
    {
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(GuzzleException $e){
            return false;
        }
        $string = (string) $response->getBody();

       if(strpos($string, "cdn.ryviu.com")) return true;

        return false;
    }
}
