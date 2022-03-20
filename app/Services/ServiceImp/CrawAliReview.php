<?php
namespace App\Services\ServiceImp;
use App\Services\CrawService;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;

class CrawAliReview implements CrawService{
    public function crawData($url){
        $client = new Client();
        try{
            $response = $client->get($url);
        }catch(RequestException $e){
            return [];
        }
        
        $htmlString = strip_tags($response->getBody(), ["<iframe>"]);
        
        // crawler
        $crawler = new Crawler($htmlString);
        $src = $crawler->filter(".aliReviewsFrame")->reduce(function (Crawler $node, $i){
            return ($node->attr("widget-id"));
        })->attr("data-ar-src");
    
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
                    'content' => $node->filter('.alireview-post > p')->text(),
                    'img'=> count($node->filter('.alireview-product-img > img'))>0?$node->filter('.alireview-product-img > img')->attr('data-src'):null,
                    'date' => $node->filter('.alireview-date')->text(),
                    'number_like' => $node->filter('.alireview-number-like')->text(),
                    'number_unlike' => $node->filter('.alireview-number-unlike')->text()
                ];
            });

            if(count($row) == 0){
                break;
            }

            $aliReviews = array_merge($aliReviews, $row);
            $currPage += 1;
        }

        return $aliReviews;
    }
}