<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlStampedReview;

class CrawlStampedReviewTest extends TestCase
{
    /**
     * @param array $shopName
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckStampedReviewsInstall
     */
    public function testCheckStampedReviewsInstall($shopName, $expectedResult){
        $crawlStampedReview = new CrawlStampedReview();
        $result = $crawlStampedReview->checkStampedReviewsInstall($shopName);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckStampedReviewsInstall(){
        return [
            ["loram-wear", false],
            ["nghialm", true],
            ["rv-test-1", true]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCrawData
     */
    public function testCrawData($inputParam, $expectedResult){
        $crawlStampedReview = new CrawlStampedReview();
        $result = $crawlStampedReview->crawData($inputParam['url'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawData(){
        return [
            [['url' => 'https://rv-test-1.myshopify.com/products/2021-women-sleeveless-vest-winter-warm-plus-size-2xl-down-cotton-padded-jacket-female-veats-mandarin-collar-sleeveless-waistcoat', 'productId' => '1'], 12],
            [['url' => 'https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift', 'productId' => '1'], 0],
            [['url' => 'https://rv-test-1.myshopify.com/products/2020-new-crystal-flower-drop-earrings-for-women-fashion-jewelry-gold-colour-rhinestones-earrings-gift-for-party-best-friend', 'productId' => '1'], 1]
        ];
    }

    /**
     * @param string $urlProduct
     * @param string @expectedProductId
     * 
     * @dataProvider providerTestGetProductIdOnStoreInstalledStamped
     */
    public function testGetProductIdOnStoreInstalledStamped($urlProduct, $expectedProductId){
        $crawlStampedReview = new CrawlStampedReview();
        $result = $crawlStampedReview->getProductIdOnStoreInstalledStamped($urlProduct);
        $this->assertEquals($expectedProductId, $result);
    }

    public function providerTestGetProductIdOnStoreInstalledStamped(){
        return [
            ['https://rv-test-1.myshopify.com/products/2021-women-sleeveless-vest-winter-warm-plus-size-2xl-down-cotton-padded-jacket-female-veats-mandarin-collar-sleeveless-waistcoat','6807065853996'],
            ['https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift', '6807811817516'],
            ['https://rv-test-1.myshopify.com/products/2020-new-crystal-flower-drop-earrings-for-women-fashion-jewelry-gold-colour-rhinestones-earrings-gift-for-party-best-friend', '6807811457068']
        ];
    }
}
