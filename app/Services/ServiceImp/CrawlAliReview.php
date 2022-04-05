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

class CrawlAliReview implements CrawlService{
    protected ReviewService $reviewService;

    public function crawlData($urlProduct, $productIdOriginal, $productId){
        $this->reviewService = new ReviewService();
        $src = $this->getUrlWidgetAliReviews($urlProduct);
        
        if(is_null($src)) return false;

        $client = new Client();
        $urlAliReview = $src."&currentPage=";
        $currPage = 1;
        $aliReviews = [];
        while(true){
            $response = $client->get($urlAliReview.$currPage);
            $html = (string) $response->getBody();
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

            foreach($row as $review){
                $this->reviewService->add([
                    'productId' => $productId,
                    'rate' => $review['rate'],
                    'authorName' => $review['author']['name'],
                    'authorAvt' => $review['author']['avt'],
                    'title' => $review['title'],
                    'content' => $review['content'],
                    'img' => $review['img'],
                    'createdAt' => $review['createdAt'],
                    'storeReply' => $review['storeReply'],
                    'storeReplyCreated' => $review['storeReplyCreated'],
                    'numberLike' => $review['numberLike'],
                    'numberDislike' => $review['numberDislike']
                ]);
            }

            $aliReviews = array_merge($aliReviews, $row);

            $currPage += 1;
            
            sleep(0.5);
        }
        // dump($aliReviews);
        return true;
    }

    public function getUrlWidgetAliReviews($urlProduct){
        $client = new Client();
        try{
            $response = $client->get($urlProduct);
        }catch(RequestException $e){
            return null;
        }
        
        $htmlString = strip_tags($response->getBody(), ["<iframe>"]);

        // crawler
        $crawler = new Crawler($htmlString);
        try{
            $src = $crawler->filter(".aliReviewsFrame")->reduce(function (Crawler $node, $i){
                return ($node->attr("widget-id"));
            })->attr("data-ar-src");
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
        $html = strip_tags((string) $response->getBody(), ["<iframe>"]);
        $crawler = new Crawler($html);

        if(count($crawler->filter('.aliReviewsFrame')) > 0){
            return true;
        }

        return false;
    }

}