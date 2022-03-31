<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlJudgeReview;

class CrawlJudgeReviewTest extends TestCase
{
    /**
     * @param array $shopName
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($shopName, $expectedResult){
        $crawlReview = new CrawlJudgeReview();
        $result = $crawlReview->checkStoreInstalledJudgeReview($shopName);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(){
        return [
            ["judge-me-demo-store", true],
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
        $crawlReview = new CrawlJudgeReview();
        $result = $crawlReview->crawData($inputParam['url'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawData(){
        return [
            [['url' => 'https://judge-me-demo-store.myshopify.com/products/local-lion-60l-tactical-backpack-molle-military-bag-men-mountainteering-large-military-backpack-rucksak-tactical-army-sport-bag', 'productId' => '1'], 44],
            [['url' => 'https://judge-me-demo-store.myshopify.com/products/desert-fox-3-4-person-dome-automatic-tent-easy-instant-setup-protable-camping-pop-up-4-seasons-backpacking-family-travel-tent', 'productId' => '1'], 7],
            [['url' => 'https://judge-me-demo-store.myshopify.com/products/local-lion-outdoor-cycling-backpack-riding-rucksacks-bicycle-road-bag-bike-knapsack-sport-camping-hiking-backpack-25l', 'productId' => '1'], 23]
        ];
    }
}
