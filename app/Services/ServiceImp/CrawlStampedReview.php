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

class CrawlStampedReview implements CrawService
{
    public function crawData($url, $productId){
        $productId = $this->getProductIdOnStoreInstalledStamped($url);

        $apiGetProductStamped = "https://stamped.io/api/widget?productId=".$productId."&apiKey=pubkey-Jzw3z4oU8ka8458Hzs08j14V4NxmvR&sId=250430&take=5&sort=featured&widgetLanguage=en&page=";

        $client = new Client();
        $currentPage = 1;
        $stampedReviews = [];
        while(1){
            $response = $client->get($apiGetProductStamped.$currentPage);
            dump($apiGetProductStamped.$currentPage);
            $html = json_decode((string) $response->getBody())->widget;
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
        dump($stampedReviews);
        return count($stampedReviews);
    }

    public function getProductIdOnStoreInstalledStamped($urlProduct){
        $client = new CLient();
        $response = $client->get($urlProduct);
        $html = strip_tags((string) $response->getBody(), ["<div>"]); // dang bug o day
        
        $crawler = new Crawler($html);
        try{
            $productId = $crawler->filter('.stamped-main-widget')->attr('data-product-id');
        }catch(\InvalidArgumentException $e){
            return null;
        }
        return $productId;

    } 

    public function checkStoreInstalledStampedReview($shopName){
        $client = new Client();
        $response = $client->get("https://".$shopName.".myshopify.com/collections/all");
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        if(count($crawler->filter('.stamped')) > 0){
            return true;
        }

        return false;
    }
}