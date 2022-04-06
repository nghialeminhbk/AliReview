<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawlLooxReview;

class CrawlLooxReviewTest extends TestCase
{
    /**
     * @param $urlProductDefault
     * @param bool $expectedResult
     *
     * @dataProvider providerTestCheckAppInstalled
     */
    public function testCheckAppInstalled($urlProductDefault, bool $expectedResult){
        $crawlReview = new CrawlLooxReview();
        $result = $crawlReview->checkAppInstalled($urlProductDefault);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAppInstalled(): array
    {
        return [
            ["https://rv-test-1.myshopify.com/products/2020-fashion-geometric-butterfly-clip-earring-for-teens-women-ear-cuffs-cool-jewelry-retro-chain-long-hanging-earings-metal-gift", false],
            ["https://loox-demo-store.myshopify.com/products/pupsy-bison-buddies", true],
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
        $crawlReview = new CrawlLooxReview();
        $result = $crawlReview->crawlData($inputParam['url'], $inputParam['originalProductId'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawlData(): array
    {
        return [
            [['url' => 'https://loox-demo-store.myshopify.com/products/white-volant-diffuser?ref=loox-wr-btn&post_id=V1DlH-RG2',
                'originalProductId' => '6819474931775',
                'productId' => '1'], true],
            [['url' => 'https://loox-demo-store.myshopify.com/products/gold-drop-bottle',
                'originalProductId' => '6819476635711',
                'productId' => '1'], true],
            [['url' => 'https://loox-demo-store.myshopify.com/products/the-bright%E2%84%A2-football',
                'originalProductId' => '6819463987263',
                'productId' => '1'], true],
            [['url' => 'https://ryviu-app.myshopify.com/products/2017-fashion-summer-domeiland-children-clothing-sets-kids-girl-outfits-print-floral-short-sleeve-cotton-tops-skirt-suits-clothes',
                'originalProductId' => '740273324131',
                'productId' => '1'], false]
        ];
    }
}
