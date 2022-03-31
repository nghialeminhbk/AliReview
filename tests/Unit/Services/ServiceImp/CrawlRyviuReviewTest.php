<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlRyviuReview;

class CrawlRyviuReviewTest extends TestCase
{
    /**
     * @param array $shopName
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($shopName, $expectedResult){
        $crawlReview = new CrawlRyviuReview();
        $result = $crawlReview->checkStoreInstalledRyviuReview($shopName);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(){
        return [
            ["ryviu-app", true],
            ["nghialm", false],
            ["rv-test-1", false]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCrawData
     */
    public function testCrawData($inputParam, $expectedResult){
        $crawlReview = new CrawlRyviuReview();
        $result = $crawlReview->crawData($inputParam['url'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawData(){
        return [
            [['url' => 'https://ryviu-app.myshopify.com/products/100-cotton-digging-the-moon-print-casual-mens-o-neck-t-shirts-fashion-mens-tops-men-t-shirt-short-sleeve-men-tshirt-2017', 'productId' => '1'], 121],
            [['url' => 'https://ryviu-app.myshopify.com/products/2015-new-summer-hong-kong-fashion-pocket-harajuku-cat-lovers-women-top-short-sleeve-t-shirt-sweet-style-black-white-grey', 'productId' => '1'], 244],
            [['url' => 'https://ryviu-app.myshopify.com/products/2017-fashion-summer-domeiland-children-clothing-sets-kids-girl-outfits-print-floral-short-sleeve-cotton-tops-skirt-suits-clothes', 'productId' => '1'], 138]
        ];
    }
}
