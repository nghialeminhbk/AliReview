<?php
namespace App\Services\ServiceImp;
use App\Services\CrawlService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;


class CrawlRivyoReview implements CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId): bool
    {
        $shopDomain = substr($urlProduct, 8, strpos($urlProduct, '/products')-8);
        $productHandle = substr($urlProduct, strrpos($urlProduct, '/') + 1, strlen($urlProduct) - strrpos($urlProduct, '/'));
        $limit = 0;
        $apiGetProductRivyo = "https://thimatic-apps.com/product_review/get_product_review_filter.php?shop=".$shopDomain."&product_handle=".$productHandle."&product_id=".$originalProductId."&limit=";
        $rivyoReviews = [];
        $client = new Client();

        while(1){
            try {
                $response = $client->post($apiGetProductRivyo . $limit);
            } catch (GuzzleException $e) {
                return false;
            }
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
                $temp['storeReply'] = [];
                $temp['storeReplyCreated'] = [];
                $temp['numberLike'] = $node->filter('.like_count_cls')->first()->text();
                $temp['numberDislike'] = $node->filter('.like_count_cls')->last()->text();
                return 1;
            });

            if(count($row) == 0) break;

            $rivyoReviews = array_merge($rivyoReviews, $row);

            $limit++;

            sleep(0.5);
        }
        return true;
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

        if(strpos($string, "thimatic-apps.com\/product_review")){
            return true;
        }

        return false;
    }
}
