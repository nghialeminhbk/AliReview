<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlStampedReview;

class CrawlFeraReviewTest extends TestCase
{
    // /**
    //  * @param array $shopName
    //  * @param bool $expectedResult
    //  * 
    //  * @dataProvider providerTestCheckInstalled
    //  */
    // public function testCheckAppInstalled($shopName, $expectedResult){
    //     $crawlReview = new CrawlStampedReview();
    //     $result = $crawlReview->checkStampedReviewsInstalled($shopName);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function providerTestCheckStampedReviewsInstalled(){
    //     return [
    //         ["loram-wear", false],
    //         ["nghialm", true],
    //         ["rv-test-1", false]
    //     ];
    // }

    // /**
    //  * @param array $inputParam
    //  * @param bool $expectedResult
    //  * 
    //  * @dataProvider providerTestCrawData
    //  */
    // public function testCrawData($inputParam, $expectedResult){
    //     $crawlReview = new CrawlStampedReview();
    //     $result = $crawlReview->crawData($inputParam['url'], $inputParam['productId']);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function providerTestCrawData(){
    //     return [
    //         [['url' => 'https://rv-test-1.myshopify.com/products/2021-women-sleeveless-vest-winter-warm-plus-size-2xl-down-cotton-padded-jacket-female-veats-mandarin-collar-sleeveless-waistcoat', 'productId' => '1'], 12],
    //         [['url' => 'https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift', 'productId' => '1'], 0],
    //         [['url' => 'https://rv-test-1.myshopify.com/products/2020-new-crystal-flower-drop-earrings-for-women-fashion-jewelry-gold-colour-rhinestones-earrings-gift-for-party-best-friend', 'productId' => '1'], 1]
    //     ];
    // }

    // /**
    //  * @param string $urlProduct
    //  * @param string @expectedProductId
    //  * 
    //  * @dataProvider providerTestGetProductIdOnStoreInstalled
    //  */
    // public function testGetProductIdOnStoreInstalled($urlProduct, $expectedProductId){
    //     $crawlReview = new CrawlStampedReview();
    //     $result = $crawlReview->getProductIdOnStoreInstalledStamped($urlProduct);
    //     $this->assertEquals($expectedProductId, $result);
    // }

    // public function providerTestGetProductIdOnStoreInstalled(){
    //     return [
    //         // ['https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry', '6807811719212'],
    //         ['https://rv-test-1.myshopify.com/products/2021-women-sleeveless-vest-winter-warm-plus-size-2xl-down-cotton-padded-jacket-female-veats-mandarin-collar-sleeveless-waistcoat','6807065853996'],
    //         ['https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift', '6807811817516'],
    //         ['https://rv-test-1.myshopify.com/products/2020-new-crystal-flower-drop-earrings-for-women-fashion-jewelry-gold-colour-rhinestones-earrings-gift-for-party-best-friend', '6807811457068']
    //     ];
    // }
}
