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

class CrawAliReview implements CrawService{
    protected ReviewService $reviewService;

    public function crawData($url, $productId){
        $this->reviewService = new ReviewService();
        $src = $this->getUrlWidgetAliReviews($url);
        
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

            foreach($row as $review){
                $this->reviewService->add([
                    'productId' => $productId,
                    'rate' => $review['rate'],
                    'authorName' => $review['author']['name'],
                    'authorAvt' => $review['author']['avt'],
                    'content' => $review['content'],
                    'img' => $review['img'],
                    'date' => $review['date'],
                    'numberLike' => $review['number_like'],
                    'numberUnlike' => $review['number_unlike']
                ]);
            }

            $currPage += 1;
        }

        return true;
    }

    public function checkAliReviewsInstall($shopName, $accessToken){
        $client = new ClientApi(false, "2022-01", $shopName, $accessToken);
        $productApi = new Product($client);
        try{
            $products = $productApi->all([
                'limit' => 1
            ]);
        }catch(ShopifyApiException $e){
            return false;
        }
        if(count($products) == 0) return false;
        $product = $products[0];
        $urlProduct = 'https://'.$shopName.'.myshopify.com/products/'.$product->handle;
        return is_null($this->getUrlWidgetAliReviews($urlProduct))?false:true;
    }

    public function getUrlWidgetAliReviews($url){
        $client = new Client();
        try{
            $response = $client->get($url);
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

}