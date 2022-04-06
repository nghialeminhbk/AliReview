<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CrawlFeraReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId): bool
    {
        $urlWidget = $this->getUrlWidgetFeraReviews($urlProduct, $originalProductId);

        if(is_null($urlWidget)) return false;

        $currentPage = 1;
        $feraReviews = [];
        $client = new Client();
        while(1){
            try {
                $response = $client->get($urlWidget . $currentPage);
            } catch (GuzzleException $e) {
                return false;
            }
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

                $feraReviews[] = $temp;
            }

            $currentPage++;
        }
        dump($feraReviews);
        return true;
    }

    public function getUrlWidgetFeraReviews($urlProduct, $productIdOriginal): ?string
    {
        $storePk = "";
        $client = new Client();
        try {
            $response = $client->get($urlProduct);
        } catch (GuzzleException $e) {
            return false;
        }

        $string = (string) $response->getBody();

        $handleString = function($string){
            $stringTemp = strpos($string, "store_pk:")?substr($string, strpos($string, "store_pk:"), 100):"";
            return explode(" ", $stringTemp);

        };
        $array = $handleString($string);
        foreach($array as $i => $item){
            if($item == "store_pk:"){
                $storePk = json_decode($array[$i+1]);
                break;
            }
        }

        return "https://api2.fera.ai/public/reviews.json?limit=5&sort_by=highest_quality&product_id=".$productIdOriginal."&public_key=".$storePk."&admin=true&api_client=fera.js-2.6.4.&page=";
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
        if(strpos($string, "https://cdn.fera.ai/js/fera.js?shop=")){
            return true;
        }
        return false;
    }

}
