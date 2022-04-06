<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlStampedReview;

class CrawlStampedReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckStampedReviewsInstall
     */
    public function testCheckStampedReviewsInstall($urlProductDefault, bool $expectedResult){
        $crawlStampedReview = new CrawlStampedReview();
        $result = $crawlStampedReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckStampedReviewsInstall(): array
    {
        return [
            ["loram-wear", false],
            ["https://www.gatomall.com/products/goat-mug-16oz", true],
            ["https://rv-test-1.myshopify.com/products/2020-new-crystal-flower-drop-earrings-for-women-fashion-jewelry-gold-colour-rhinestones-earrings-gift-for-party-best-friend", true]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData(array $inputParam, bool $expectedResult){
        $crawlStampedReview = new CrawlStampedReview();
        $result = $crawlStampedReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://rv-test-1.myshopify.com/products/2021-women-sleeveless-vest-winter-warm-plus-size-2xl-down-cotton-padded-jacket-female-veats-mandarin-collar-sleeveless-waistcoat',
                'originalProductId' => '6807065853996',
                'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift',
                'originalProductId' => '6807811817516',
                'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/2020-new-crystal-flower-drop-earrings-for-women-fashion-jewelry-gold-colour-rhinestones-earrings-gift-for-party-best-friend',
                'originalProductId' => '6807811457068',
                'productId' => '1'], true]
        ];
    }
}
