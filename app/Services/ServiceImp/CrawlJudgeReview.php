<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException as InvalidArgumentExceptionAlias;
use Symfony\Component\DomCrawler\Crawler;


class CrawlJudgeReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId){
        $urlWidget = $this->getUrlWidgetJudgeReviews($urlProduct);

        if(is_null($urlWidget)) return false;

        $currentPage = 1;
        $judgeReviews = [];
        $client = new Client();
        while(1){
            try {
                $response = $client->get($urlWidget . $currentPage);
            } catch (GuzzleException $e) {
                return false;
            }
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
                    'storeReply' => count($node->filter('.jdgm-rev__reply-content'))>0?$node->filter('.jdgm-rev__reply-content')->each(function(Crawler $node){ return $node->text();}):[],
                    'storeReplyCreated' => [],
                    'numberLike' => null,
                    'numberDislike' => null
                ];
            });

            if(count($row) == 0) break;

            // foreach($row as$review){
                // save to db
            // }

            $judgeReviews = array_merge($judgeReviews, $row);

            $currentPage++;
        }
        dump(count($judgeReviews));
        return true;
    }

    public function getUrlWidgetJudgeReviews($url): ?string
    {
        $shopDomain = substr($url, 8, strpos($url, '/products')-8);
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(GuzzleException $e){
            return null;
        }
        $html = (string) $response->getBody();

        // crawler
        $crawler = new Crawler($html);
        try{
            $productId = $crawler->filter('#judgeme_product_reviews')->attr('data-id');
        }catch(InvalidArgumentExceptionAlias $e){
            return null;
        }

        return "https://judge.me/reviews/reviews_for_widget?url=".$shopDomain."&shop_domain=".$shopDomain."&platform=shopify&per_page=5&product_id=".$productId."&page=";

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

        if(strpos($string, "cdn.judge.me\/assets")){
            return true;
        }

        return false;
    }
}
