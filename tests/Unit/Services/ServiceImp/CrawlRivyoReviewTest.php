<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlRivyoReview;

class CrawlRivyoReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckAppInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlReview = new CrawlRivyoReview();
        $result = $crawlReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAppInstalled(): array
    {
        return [
            ["https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift", true],
            ["https://thimatic-product-review.myshopify.com/products/black-t-shirt", true],
            ["rv-test-1", false]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCrawlData
     */
    public function testCrawlData(array $inputParam, bool $expectedResult){
        $crawlReview = new CrawlRivyoReview();
        $result = $crawlReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://thimatic-product-review.myshopify.com/products/black-t-shirt',
                'originalProductId' => '4615395082303',
                'productId' => '1'], true],
            [['url' => 'https://thimatic-product-review.myshopify.com/products/orange-t-shirt',
                'originalProductId' => '4615395770431',
                'productId' => '1'], true],
            [['url' => 'https://thimatic-product-review.myshopify.com/products/yellow-t-shirt',
                'originalProductId' => '4615395934271',
                'productId' => '1'], true]
        ];
    }
}
