<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException as InvalidArgumentExceptionAlias;
use Symfony\Component\DomCrawler\Crawler;


class CrawlLooxReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId){
        $urlWidget = $this->getUrlWidgetLooxReviews($urlProduct, $originalProductId);

        if(is_null($urlWidget)) return false;

        $client = new Client();
        $urlWidgetPagination = $urlWidget.'?page=';
        $currentPage = 1;
        $looxReviews = [];
        while(1){
            try {
                $response = $client->get($urlWidgetPagination . $currentPage);
            } catch (GuzzleException $e) {
                return false;
            }
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);
            $row = $crawler->filter('.grid-item-wrap')->each(function( Crawler $node){
                return [
                    'img' => count($node->filter('.item-img > img'))>0?$node->filter('.item-img > img')->attr('src'):null,
                    'authorName' => $node->filter('.title')->text(),
                    'createdAt' => $node->filter('.time')->text(),
                    'rate' => substr($node->filter('.stars')->attr('aria-label'), 0, 1),
                    'title' => null,
                    'content' => $node->filter('.main-text')->text(),
                    'storeReply' => [],
                    'storeReplyCreated' => [],
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
         dump(count($looxReviews));
        return true;
    }

    public function getUrlWidgetLooxReviews($urlProduct, $productIdOriginal): ?string
    {
        $client = new Client();
        try{
            $response = $client->get($urlProduct);
        }catch(GuzzleException $e){
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
        }catch(InvalidArgumentExceptionAlias $e){
            return null;
        }

        return $src;
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

        if(strpos($string, "loox.io/widget")) return true;

        return false;
    }
}
