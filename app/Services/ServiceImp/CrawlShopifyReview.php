<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;

class CrawlShopifyReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId): bool
    {
        $apiWidget = $this->apiShopifyReview($urlProduct, $originalProductId);

        if(is_null($apiWidget)) return false;
        $client = new Client();
        $currentPage = 1;
        $shopifyReviews = [];
        while(1){
            try {
                $response = $client->get($apiWidget . $currentPage);
                $html = json_decode((string) $response->getBody())->reviews;
            } catch (GuzzleException $e) {
                return false;
            }
            $crawler = new Crawler($html);
            $row = $crawler
            ->filter('.spr-review')
            ->each(function( Crawler $node){
                    return [
                        'img' => null,
                        'authorName' => $node->filter('.spr-review-header-byline strong')->first()->text(),
                        'authorAvt' => null,
                        'createdAt' => $node->filter('.spr-review-header-byline strong')->last()->text(),
                        'rate' => count($node->filter('.spr-icon-star')),
                        'title' => $node->filter('.spr-review-header-title')->text(),
                        'content' => $node->filter('.spr-review-content-body')->text(),
                        'storeReply' => count($node->filter('.spr-review-reply'))>0?$node->filter('.spr-review-reply')->each(function(Crawler $node){
                            return $node->text();
                        }):null,
                        'storeReplyCreated' => [],
                        'numberLike' => null,
                        'numberDislike' => null
                    ];
            });

            if(count($row) == 0) break;

            $shopifyReviews = array_merge($shopifyReviews, $row);

            $currentPage++;

        }
        // dump($yotpoReviews);
        return true;
    }

    public function apiShopifyReview($urlProduct, $productIdOriginal): ?string
    {
        $shopDomain = substr($urlProduct, 8, strpos($urlProduct, '/products')-8);
        return "https://productreviews.shopifycdn.com/proxy/v4/reviews?product_id=".$productIdOriginal."&shop=".$shopDomain."&page=";
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
        if(strpos($string, "productreviews.shopifycdn.com")){
            return true;
        }

        return false;
    }
}
