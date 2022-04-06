<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlLaiReview;

class CrawlLaiReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckAppInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlAliReview = new CrawlLaiReview();
        $result = $crawlAliReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAppInstalled(): array
    {
        return [
            ['https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry', true],
            ['https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', true],
            ['https://rv-test-2.myshopify.com/products/1-piece-stainless-steel-painless-ear-cldsaadsdsip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', false],
            ['https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift', true]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData(array $inputParam, bool $expectedResult){
        $crawAliReview = new CrawlLaiReview();
        $result = $crawAliReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts',
                'originalProductId' => '6807811588140',
                'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry',
                'originalProductId' => '6807811719212',
                'productId' => '2'], true],
            [['url' => 'https://rv-test-2.myshopify.com/products/1-pcs-medical-stainleddss-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry',
                'originalProductId' => '6807811719212',
                'productId' => '1'], true]
        ];
    }
}
