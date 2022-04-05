<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlAliReview;

class CrawlLaiReviewTest extends TestCase
{
    /**
     * @param array $infoShop
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckAppInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, $expectedResult){
        $crawlAliReview = new CrawlAliReview();
        $result = $crawlAliReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAppInstalled(){
        return [
            ['https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry', true],
            ['https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', true],
            ['https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-cldsaadsdsip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', false],
            ['https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift', true]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData($inputParam, $expectedResult){
        $crawAliReview = new CrawAliReview();
        $result = $crawAliReview->crawlData($inputParam['url'], $inputParam['productIdOriginal'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(){
        return [
            [['url' => 'https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts','productIdOriginal' => '6807811588140', 'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry','productIdOriginal' => '6807811719212', 'productId' => '2'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/1-pcs-medical-stainleddss-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry','productIdOriginal' => '6807811719212', 'productId' => '1'], false]
        ];
    }
}
