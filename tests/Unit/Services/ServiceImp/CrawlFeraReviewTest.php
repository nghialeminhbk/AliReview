<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlFeraReview;

class CrawlFeraReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckReviewsInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlReview = new CrawlFeraReview();
        $result = $crawlReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckReviewsInstalled(): array
    {
        return [
            ["https://ali-reviews-fireapps.myshopify.com/products/charmsmic-new-striped", false],
            ["https://rv-test-1.myshopify.com/products/abstract-v-back-cami", true],
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
        $crawlReview = new CrawlFeraReview();
        $result = $crawlReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://rv-test-1.myshopify.com/products/1-pcs-medical-stainless-steel-crystal-zircon-ear-studs-earrings-for-women-men-4-prong-tragus-cartilage-piercing-jewelry',
                'originalProductId' => '6807811719212',
                'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts',
                'originalProductId' => '6807811588140',
                'productId' => '1'], true],
            [['url' => 'https://ryviu-app.myshopify.com/products/2017-fashion-summer-domeiland-children-clothing-sets-kids-girl-outfits-print-floral-short-sleeve-cotton-tops-skirt-suits-clothes',
                'originalProductId' => '740298883171',
                'productId' => '1'], true]
        ];
    }
}
