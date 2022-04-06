<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CrawlAutomizelyReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId): bool
    {
        $shopName = substr($urlProduct, 8, strpos($urlProduct, '.myshopify.com')-8);

        $apiReviews = "https://api.automizely.com/reviews/v1/shopper/reviews?store_key=".$shopName."&product_external_id=".$originalProductId."&limit=5&page=";

        $currentPage = 1;
        $automizelyReviews = [];
        $client = new Client();
        while(1){
            try {
                $response = $client->get($apiReviews);
            } catch (GuzzleException $e) {
                return false;
            }
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

                 $automizelyReviews[] = $temp;
            }

            $currentPage++;

            sleep(0.5);
        }
        dump(count($automizelyReviews));
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

        $html = strip_tags((string) $response->getBody(), ["<script>"]);
        if(strpos($html, "widgets.automizely.com\/reviews")) {
            return true;
        }

        return false;
    }
}
