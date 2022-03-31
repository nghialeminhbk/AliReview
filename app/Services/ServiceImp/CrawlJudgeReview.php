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

class CrawlJudgeReview implements CrawService
{
    public function crawData($url, $productId){
        $urlWidget = $this->getUrlWidgetJudgeReviews($url);

        if(is_null($urlWidget)) return false;

        $currentPage = 1;
        $judgeReviews = [];
        $client = new Client();
        while(1){
            $response = $client->get($urlWidget.$currentPage);
            $html = json_decode((string) $response->getBody())->html;
            $crawler = new Crawler($html);

            $row = $crawler->filter('.jdgm-divider-top')->each(function( Crawler $node){
                return [
                    'img' => count($node->filter('.jdgm-rev__pic-img'))>0?$node->filter('.jdgm-rev__pic-img')->each(function(Crawler $node){
                        return $node->attr('data-src');
                    }):null,
                    'authorName' => $node->filter('.jdgm-rev__author')->text(),
                    'authorAvt' => is_null($node->filter('.jdgm-rev__icon')->attr('data-gravatar-hash'))?null:"https://secure.gravatar.com/avatar/".$node->filter('.jdgm-rev__icon')->attr('data-gravatar-hash').".png",
                    'createdAt' => $node->filter('.jdgm-rev__timestamp')->attr('data-content'),
                    'rate' => $node->filter('.jdgm-rev__rating')->attr('data-score'),
                    'title' => $node->filter('.jdgm-rev__title')->text(),
                    'content' => $node->filter('.jdgm-rev__body > p')->text(),
                    'storeReply' => count($node->filter('.jdgm-rev__reply-content'))>0?$node->filter('.jdgm-rev__reply-content')->text():null,
                    'storeReplyCreated' => null,
                    'numberLike' => null,
                    'numberDislike' => null
                ];
            });

            if(count($row) == 0) break;

            foreach($row as$review){

            }

            $judgeReviews = array_merge($judgeReviews, $row);

            $currentPage++;
        }
        // dump($judgeReviews);
        return count($judgeReviews);
    }

    public function getUrlWidgetJudgeReviews($url){
        $shopDomain = substr($url, 8, strpos($url, '/products')-8);
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(RequestException $e){
            return null;
        }
        $html = (string) $response->getBody();

        // crawler
        $crawler = new Crawler($html);
        try{
            $productId = $crawler->filter('#judgeme_product_reviews')->attr('data-id');
        }catch(\InvalidArgumentException $e){
            return null;
        }

        return "https://judge.me/reviews/reviews_for_widget?url=".$shopDomain."&shop_domain=".$shopDomain."&platform=shopify&per_page=5&product_id=".$productId."&page=";

    }

    public function checkStoreInstalledJudgeReview($shopName){
        $client = new Client();
        try{
            $response = $client->get("https://".$shopName.".myshopify.com/collections/all");
        }catch(RequestException $e){
            return false;
        }
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        if(count($crawler->filter('.jdgm-widget')) > 0){
            return true;
        }

        return false;
    }
}