<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlLooxReview;

class CrawlLooxReviewTest extends TestCase
{
    /**
     * @param array $shopName
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($shopName, $expectedResult){
        $crawlReview = new CrawlLooxReview();
        $result = $crawlReview->checkStoreInstalledLooxReview($shopName);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(){
        return [
            ["loox-demo-store", true],
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
        $crawlReview = new CrawlLooxReview();
        $result = $crawlReview->crawData($inputParam['url'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawData(){
        return [
            [['url' => 'https://loox-demo-store.myshopify.com/products/white-volant-diffuser?ref=loox-wr-btn&post_id=V1DlH-RG2', 'productId' => '1'], 154],
            [['url' => 'https://loox-demo-store.myshopify.com/products/gold-drop-bottle', 'productId' => '1'], 30],
            [['url' => 'https://loox-demo-store.myshopify.com/products/the-bright%E2%84%A2-football', 'productId' => '1'], 63]
        ];
    }
}
