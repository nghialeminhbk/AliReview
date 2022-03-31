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

class CrawlLaiReview implements CrawService
{
    public function crawData($url, $productId){
        $urlWidget = $this->getUrlWidgetLooxReviews($url);

        if(is_null($urlWidget)) return false;

        $client = new Client();
        $urlWidgetPagination = $urlWidget.'?page=';
        $currentPage = 1;
        $yotpoReviews = [];
        while(1){
            $response = $client->get($urlWidgetPagination.$currentPage);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);
            $rows = $crawler->filter('.grid-item-wrap')->each(function( Crawler $node){
                return [
                    'image_review' => count($node->filter('.item-img > img'))>0?$node->filter('.item-img > img')->attr('src'):null,
                    'author' => $node->filter('.title')->text(),
                    'datetime' => $node->filter('.time')->text(),
                    'rate' => substr($node->filter('.stars')->attr('aria-label'), 0, 1),
                    'content' => $node->filter('.main-text')->text()
                ];
            });

            if(count($rows) == 0) break;

            $yotpoReviews = array_merge($yotpoReviews, $rows);

            $currentPage++;

        }
        dump($yotpoReviews);
        return true;
    }

    public function getUrlWidgetLaiReviews($url){
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(RequestException $e){
            return null;
        }
        
        $htmlString = strip_tags($response->getBody(), ["<script>", "<div>"]);

        // crawler
        $crawler = new Crawler($htmlString);
        try{
            $stringTemp = $crawler->filter("script")->last()->attr('src');
            $index = strrpos($stringTemp, '/');
            $preSrc = substr($stringTemp, 0, $index + 1);
            $productId = $crawler->filter('#looxReviews')->attr('data-product-id');
            $src = $preSrc."reviews/".$productId;
        }catch(\InvalidArgumentException $e){
            return null;
        }
        
        return $src;
    } 

    public function checkStoreInstalledLaiReview($shopName){
        $client = new Client();
        $response = $client->get("https://".$shopName.".myshopify.com/collections/all");
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        if(count($crawler->filter('.lai-reviewPop')) > 0){
            return true;
        }

        return false;
    }
}