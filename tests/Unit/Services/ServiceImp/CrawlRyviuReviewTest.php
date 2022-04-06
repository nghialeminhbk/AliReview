<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlRyviuReview;

class CrawlRyviuReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlReview = new CrawlRyviuReview();
        $result = $crawlReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(): array
    {
        return [
            ["https://judge-me-demo-store.myshopify.com/products/local-lion-outdoor-cycling-backpack-riding-rucksacks-bicycle-road-bag-bike-knapsack-sport-camping-hiking-backpack-25l", false],
            ["https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry", true],
            ["https://ryviu-app.myshopify.com/products/100-cotton-digging-the-moon-print-casual-mens-o-neck-t-shirts-fashion-mens-tops-men-t-shirt-short-sleeve-men-tshirt-2017", true]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData(array $inputParam, bool $expectedResult){
        $crawlReview = new CrawlRyviuReview();
        $result = $crawlReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://ryviu-app.myshopify.com/products/100-cotton-digging-the-moon-print-casual-mens-o-neck-t-shirts-fashion-mens-tops-men-t-shirt-short-sleeve-men-tshirt-2017',
                'originalProductId' => '740298883171',
                'productId' => '1'], true],
            [['url' => 'https://ryviu-app.myshopify.com/products/2015-new-summer-hong-kong-fashion-pocket-harajuku-cat-lovers-women-top-short-sleeve-t-shirt-sweet-style-black-white-grey',
                'originalProductId' => '12455286923',
                'productId' => '1'], true],
            [['url' => 'https://ryviu-app.myshopify.com/products/2017-fashion-summer-domeiland-children-clothing-sets-kids-girl-outfits-print-floral-short-sleeve-cotton-tops-skirt-suits-clothes',
                'originalProductId' => '740273324131',
                'productId' => '1'], true]
        ];
    }
}
