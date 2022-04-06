<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlAliReview;

class CrawAliReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckAppInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlAliReview = new CrawlAliReview();
        $result = $crawlAliReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAppInstalled(): array
    {
        return [
            ["https://ali-reviews-fireapps.myshopify.com/products/charmsmic-new-striped", true],
            ["https://ali-reviews-fireapps.myshopify.com/products/bamoer-authentic-100", true],
            ["https://ali-reviews-fissreapps.myshopify.com/products/bamoer-authentic-100", false],
            ["https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry", true]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData(array $inputParam, bool $expectedResult){
        $crawAliReview = new CrawlAliReview();
        $result = $crawAliReview->crawlData($inputParam['url'], $inputParam['productIdOriginal'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', 'productIdOriginal' => '6807811588140', 'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/17km-gold-leaves-ear-cuff-black-non-piercing-ear-clips-fake-cartilage-earrings-clip-earrings-for-women-men-who', 'productIdOriginal' => '6807811588140', 'productId' => '1'], false],
            [['url' => 'https://rv-test-1.myshopify.com/products/abstract-v-back-cami', 'productIdOriginal' => '4820800241708', 'productId' => '1'], true]
        ];
    }
}
