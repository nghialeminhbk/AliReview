<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlJudgeReview;

class CrawlJudgeReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlReview = new CrawlJudgeReview();
        $result = $crawlReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(): array
    {
        return [
            ["https://judge-me-demo-store.myshopify.com/products/local-lion-outdoor-cycling-backpack-riding-rucksacks-bicycle-road-bag-bike-knapsack-sport-camping-hiking-backpack-25l", true],
            ["https://rv-test-1.myshopify.com/products/abstract-v-back-cami", true],
            ["nnasndlsndslndsalsadldasl", false]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData(array $inputParam, bool $expectedResult){
        $crawlReview = new CrawlJudgeReview();
        $result = $crawlReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://judge-me-demo-store.myshopify.com/products/local-lion-60l-tactical-backpack-molle-military-bag-men-mountainteering-large-military-backpack-rucksak-tactical-army-sport-bag',
                'originalProductId' => '3784779071548',
                'productId' => '1'], true],
            [['url' => 'https://judge-me-demo-store.myshopify.com/products/desert-fox-3-4-person-dome-automatic-tent-easy-instant-setup-protable-camping-pop-up-4-seasons-backpacking-family-travel-tent',
                'originalProductId' => '3784818917436',
                'productId' => '1'], true],
            [['url' => 'https://judge-me-demo-store.myshopify.com/products/local-lion-outdoor-cycling-backpack-riding-rucksacks-bicycle-road-bag-bike-knapsack-sport-camping-hiking-backpack-25l',
                'originalProductId' => '3784779104316',
                'productId' => '1'], true]
        ];
    }
}
