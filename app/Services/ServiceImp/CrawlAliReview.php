<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException as InvalidArgumentExceptionAlias;
use Symfony\Component\DomCrawler\Crawler;
use App\Services\ReviewService;

class CrawlAliReview implements CrawlService{
    protected ReviewService $reviewService;

    public function crawlData($urlProduct, $originalProductId, $productId): bool
    {
        $this->reviewService = new ReviewService();
        $src = $this->getUrlWidgetAliReviews($urlProduct);

        if(is_null($src)) return false;

        $client = new Client();
        $urlAliReview = $src."&currentPage=";
        $currPage = 1;
        $aliReviews = [];
        while(true){
            try {
                $response = $client->get($urlAliReview . $currPage);
                $html = (string) $response->getBody();
            } catch (GuzzleException $e) {
                return false;
            }

            $crawler = new Crawler($html);
            $row = $crawler->filter('.alireview-row')->each(function( Crawler $node ){
                return [
                    'rate' => $node->filter('.alr-rating')->attr('value'),
                    'author' => [
                        'avt' => $node->filter('.alireview-avatar')->attr('data-src'),
                        'name' => $node->filter('.alireview-author')->text()
                    ],
                    'title' => null,
                    'content' => $node->filter('.alireview-post > p')->text(),
                    'img'=> count($node->filter('.alireview-product-img > img'))>0?json_encode($node->filter('.alireview-product-img > img')->each(function(Crawler $node){
                        return $node->attr('data-src');
                    })):null,
                    'createdAt' => $node->filter('.alireview-date')->text(),
                    'storeReply' => null,
                    'storeReplyCreated' => null,
                    'numberLike' => $node->filter('.alireview-number-like')->text(),
                    'numberDislike' => $node->filter('.alireview-number-unlike')->text()
                ];
            });

            if(count($row) == 0){
                break;
            }

//            foreach($row as $review){
//                 save to db
//                 $this->reviewService->add([
//                     'productId' => $productId,
//                     'rate' => $review['rate'],
//                     'authorName' => $review['author']['name'],
//                     'authorAvt' => $review['author']['avt'],
//                     'title' => $review['title'],
//                     'content' => $review['content'],
//                     'img' => $review['img'],
//                     'createdAt' => $review['createdAt'],
//                     'storeReply' => $review['storeReply'],
//                     'storeReplyCreated' => $review['storeReplyCreated'],
//                     'numberLike' => $review['numberLike'],
//                     'numberDislike' => $review['numberDislike']
//                 ]);
//            }

            $aliReviews = array_merge($aliReviews, $row);

            $currPage += 1;

            sleep(0.5);
        }
         dump(count($aliReviews));
        return true;
    }

    public function getUrlWidgetAliReviews($urlProduct): ?string
    {
        $client = new Client();
        try{
            $response = $client->get($urlProduct);
        }catch(GuzzleException $e){
            return null;
        }

        $htmlString = strip_tags($response->getBody(), ["<iframe>"]);

        // crawler
        $crawler = new Crawler($htmlString);
        try{
            $src = $crawler->filter(".aliReviewsFrame")->reduce(function (Crawler $node){
                return ($node->attr("widget-id"));
            })->attr("data-ar-src");
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

        if(strpos($string, "widget.alireviews.io/widget")){
            return true;
        }

        return false;
    }

}
