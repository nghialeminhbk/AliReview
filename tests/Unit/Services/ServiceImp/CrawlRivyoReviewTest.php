<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlRivyoReview;

class CrawlRivyoReviewTest extends TestCase
{
    /**
     * @param array $shopName
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($shopName, $expectedResult){
        $crawlReview = new CrawlRivyoReview();
        $result = $crawlReview->checkStoreInstalledRivyoReview($shopName);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(){
        return [
            ["thimatic-product-review", true],
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
        $crawlReview = new CrawlRivyoReview();
        $result = $crawlReview->crawData($inputParam['url'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawData(){
        return [
            [['url' => 'https://thimatic-product-review.myshopify.com/products/black-t-shirt', 'productId' => '1'], 17],
            [['url' => 'https://thimatic-product-review.myshopify.com/products/orange-t-shirt', 'productId' => '1'], 9],
            [['url' => 'https://thimatic-product-review.myshopify.com/products/yellow-t-shirt', 'productId' => '1'], 17]
        ];
    }
}
