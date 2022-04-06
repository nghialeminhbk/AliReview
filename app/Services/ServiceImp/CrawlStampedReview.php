<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class CrawlStampedReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId): bool
    {
        $apiGetProductStamped = $this->getApiGetProductsStamped($urlProduct, $originalProductId);

        $client = new Client();
        $currentPage = 1;
        $stampedReviews = [];
        while(1){
            try {
                $response = $client->get($apiGetProductStamped . $currentPage);
                $html = json_decode((string) $response->getBody())->widget;
            } catch (GuzzleException $e) {
                return false;
            }
            $crawler = new Crawler($html);

            $row = $crawler->filter('.stamped-review')->each(function( Crawler $node){
                return [
                    'rate' => $node->filter('.stamped-starratings')->attr('data-rating'),
                    'author_name' => $node->filter('.author')->text(),
                    'author_avt' => $node->filter('.stamped-review-avatar-content')->text(),
                    'title' => $node->filter('.stamped-review-header-title')->text(),
                    'content' => $node->filter('.stamped-review-content-body')->text(),
                    'img' => count($node->filter('.stamped-review-image > a'))>0?$node->filter('.stamped-review-image > a')->each(function(Crawler $node){
                        return $node->attr('href');
                    }):null,
                    'created_at' => $node->filter('.created')->text(),
                    'store_reply' => count($node->filter('.stamped-review-reply'))>0?$node->filter('.stamped-review-reply')->each(function(Crawler $node){
                        return $node->filter('.stamped-review-content-body')->text();
                    }):null,
                    'store_reply_created' => count($node->filter('.stamped-review-reply'))>0?$node->filter('.stamped-review-reply')->each(function(Crawler $node){
                        return $node->filter('.created')->text();
                    }):null,
                    'number_like' => $node->filter('.stamped-thumbs-up > i')->text(),
                    'number_dislike' => $node->filter('.stamped-thumbs-down > i')->text()
                ];
            });

            if(count($row) == 0) break;

            // foreach($row as $review){
            //     // save review to db
            // }

            $stampedReviews = array_merge($stampedReviews, $row);

            $currentPage++;

            sleep(0.5);
        }
//        dump($stampedReviews);
        return true;
    }

    public function getApiGetProductsStamped($urlProduct, $productIdOriginal): string
    {
        $client = new Client();
        $shopName = substr($urlProduct, 8, strpos($urlProduct, ".myshopify.com") - 8);
        try {
            $response = $client->get("https://stamped.io/api/getappkey?shopShopifyDomain=" . $shopName . ".myshopify.com");
            $data = json_decode((string) $response->getBody());
        } catch (GuzzleException $e) {
            return false;
        }
        $apiKey = $data->apiKey;
        $sId = $data->sId;

        return "https://stamped.io/api/widget?productId=".$productIdOriginal."&apiKey=".$apiKey."&sId=".$sId."&take=5&sort=featured&widgetLanguage=en&page=";
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
        if(strpos($string, "stamped.io")) return true;

        return false;
    }
}
