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
     * @dataProvider providerTestCheckAppInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, $expectedResult){
        $crawlReview = new CrawlLooxReview();
        $result = $crawlReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAppInstalled(){
        return [
            ["https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift", false],
            ["https://loox-demo-store.myshopify.com/products/pupsy-bison-buddies", true],
            ["rv-test-1", false]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData($inputParam, $expectedResult){
        $crawlReview = new CrawlLooxReview();
        $result = $crawlReview->crawData($inputParam['url'], $inputParam['productIdOriginal'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(){
        return [
            [['url' => 'https://loox-demo-store.myshopify.com/products/white-volant-diffuser?ref=loox-wr-btn&post_id=V1DlH-RG2', 'productIdOriginal' => '6819474931775' ,'productId' => '1'], 154],
            [['url' => 'https://loox-demo-store.myshopify.com/products/gold-drop-bottle','productIdOriginal' => '6819476635711', 'productId' => '1'], 30],
            [['url' => 'https://loox-demo-store.myshopify.com/products/the-bright%E2%84%A2-football', 'productIdOriginal' => '6819463987263', 'productId' => '1'], 63]
        ];
    }
}
