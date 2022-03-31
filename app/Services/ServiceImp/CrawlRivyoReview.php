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

class CrawlRivyoReview implements CrawService
{
    public function crawData($url, $productId){
        $shopDomain = substr($url, 8, strpos($url, '/products')-8);;
        $productHandle = substr($url, strrpos($url, '/') + 1, strlen($url) - strrpos($url, '/'));
        $productId = $this->getProductIdOnStoreInstalledRivyo($url);
        $limit = 0;
        $apiGetProductRivyo = "https://thimatic-apps.com/product_review/get_product_review_filter.php?shop=".$shopDomain."&product_handle=".$productHandle."&product_id=".$productId."&limit="; 
        $rivyoReviews = [];
        $client = new Client();

        while(1){
            $response = $client->post($apiGetProductRivyo.$limit);
            $html = (string) $response->getBody();
            $crawler = new Crawler($html);

            $row = $crawler->filter(".wc_review_grid_item")->each(function(Crawler $node){
                $temp['rate'] = 5 - count($node->filter('.wc_icon_empty'));
                $temp['authorName'] = $node->filter('.wc_review_author_name')->text();
                $temp['authorAvt'] = count($node->filter('.wc_grid_author_img > figure > img'))>0?$node->filter('.wc_grid_author_img > figure > img')->attr('href'):null; // bug ko filter dc
                $temp['title'] = $node->filter('.wc_review_boby_title')->text();
                $temp['content'] = $node->filter('.wc_review_text > p')->text(); // bug read more
                $temp['img'] = count($node->filter('.wc_review_image'))>0?$node->filter('.wc_review_image')->each(function(Crawler $node){
                    return $node->filter('img')->attr('href');
                }):null;
                $temp['createdAt'] = $node->filter('.wc_review_date')->text();
                $temp['storeReply'] = null;
                $temp['storeReplyCreated'] = null;
                $temp['numberLike'] = $node->filter('.like_count_cls')->first()->text();
                $temp['numberDislike'] = $node->filter('.like_count_cls')->last()->text();
                return 1;
            });
            
            if(count($row) == 0) break;

            $rivyoReviews = array_merge($rivyoReviews, $row);

            $limit++;

            sleep(0.5);
        }
        return count($rivyoReviews);
    }

    public function getProductIdOnStoreInstalledRivyo($urlProduct){
        $client = new CLient();
        $response = $client->get($urlProduct);
        $html = (string) $response->getBody(); 
        
        $crawler = new Crawler($html);
        try{
            $productId = $crawler->filter('#wc_review_section')->attr('data-product_id');
        }catch(\InvalidArgumentException $e){
            return null;
        }
        return $productId;
    }

    public function checkStoreInstalledRivyoReview($shopName){
        $client = new Client();
        $response = $client->get("https://".$shopName.".myshopify.com/collections/all");
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        if(count($crawler->filter('.wc_product_review_badge')) > 0){
            return true;
        }

        return false;
    }
}