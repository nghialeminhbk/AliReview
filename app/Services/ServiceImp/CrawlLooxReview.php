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

class CrawlLooxReview implements CrawlService
{
    public function crawlData($urlProduct, $productIdOriginal, $productId){
        $urlWidget = $this->getUrlWidgetLooxReviews($urlProduct, $productIdOriginal);

        if(is_null($urlWidget)) return false;

        $client = new Client();
        $urlWidgetPagination = $urlWidget.'?page=';
        $currentPage = 1;
        $looxReviews = [];
        while(1){
            $response = $client->get($urlWidgetPagination.$currentPage);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);
            $row = $crawler->filter('.grid-item-wrap')->each(function( Crawler $node){
                return [
                    'immg' => count($node->filter('.item-img > img'))>0?$node->filter('.item-img > img')->attr('src'):null,
                    'authorName' => $node->filter('.title')->text(),
                    'createdAt' => $node->filter('.time')->text(),
                    'rate' => substr($node->filter('.stars')->attr('aria-label'), 0, 1),
                    'title' => null,
                    'content' => $node->filter('.main-text')->text(),
                    'storeReply' => null,
                    'storeReplyCreated' => null,
                    'numberLike' => null,
                    'numberDislike' => null,
                ];
            });

            if(count($row) == 0) break;

            // foreach($row as $review){
            //     // save review to db
            // }

            $looxReviews = array_merge($looxReviews, $row);

            $currentPage++;

        }
        // dump($looxReviews);
        return count($looxReviews);
    }

    public function getUrlWidgetLooxReviews($urlProduct, $productIdOriginal){
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
            $src = $preSrc."reviews/".$productIdOriginal;
        }catch(\InvalidArgumentException $e){
            return null;
        }
        
        return $src;
    } 

    public function checkAppInstalled($urlProductDefault){
        $client = new Client();
        try{
            $response = $client->get($urlProductDefault);
        }catch(RequestException $e){
            return false;
        }
        $string = (string) $response->getBody();

        if(strpos($string, "loox.io/widget")) return true;

        return false;
    }
}