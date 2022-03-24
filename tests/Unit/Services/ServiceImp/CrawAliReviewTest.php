<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ServiceImp\CrawAliReview;

class CrawAliReviewTest extends TestCase
{
    /**
     * @param array $infoShop
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCheckAliReviewsInstall
     */
    public function testCheckAliReviewsInstall($infoShop, $expectedResult){
        $crawAliReview = new CrawAliReview();
        $result = $crawAliReview->checkAliReviewsInstall($infoShop['shopName'], $infoShop['accessToken']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCheckAliReviewsInstall(){
        return [
            [['shopName' => 'nghialm', 'accessToken' => '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'], false],
            [['shopName' => 'rv-test-1', 'accessToken' => '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'], true],
            [['shopName' => 'rv-test-2', 'accessToken' => 'shpat_17ab166f1c41bd0d73c29cfdb40e673'], false]
        ];
    }

    /**
     * @param array $inputParam
     * @param bool $expectedResult
     * 
     * @dataProvider providerTestCrawData
     */
    public function testCrawData($inputParam, $expectedResult){
        $crawAliReview = new CrawAliReview();
        $result = $crawAliReview->crawData($inputParam['url'], $inputParam['productId']);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerTestCrawData(){
        return [
            // [['url' => 'https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', 'productId' => '1'], true],
            [['url' => 'https://rv-test-1.myshopify.com/products/17km-gold-leaves-ear-cuff-black-non-piercing-ear-clips-fake-cartilage-earrings-clip-earrings-for-women-men-who', 'productId' => '1'], false],
            [['url' => 'https://rv-test-1.myshopify.com/products/abstract-v-back-cami', 'productId' => '1'], true]
        ];
    }

    /**
     * @param string $urlProduct
     * @param string @expectedurlWidget
     * 
     * @dataProvider providerTestGetUrlWidgetAliReviews
     */
    public function testGetUrlWidgetAliReviews($urlProduct, $expectedUrlWidget){
        $crawAliReview = new CrawAliReview();
        $result = $crawAliReview->getUrlWidgetAliReviews($urlProduct);
        $this->assertEquals($expectedUrlWidget, $result);
    }

    public function providerTestGetUrlWidgetAliReviews(){
        return [
            ['https://rv-test-1.myshopify.com/products/1-piece-stainless-steel-painless-ear-clip-earrings-for-men-women-punk-silver-color-non-piercing-fake-earrings-jewelry-gifts', 'https://widget.alireviews.io/widget/review-widget?shop_id=36179214380&widget_id=147948&type_page=product&product_id=6807811588140&isAdminLogin=false&star=all&customer_id=&product_in_cart=&num_rand=0&total_order_values=0&avg_order_value=0&tag=&country=&last_purchase=&t=1647943423'],
            ['https://rv-test-1.myshopify.com/products/1pcs-pvc-new-style-game-machine-keychain-amp-keyring-cute-gamepad-joystick-key-chain-keychains-bag-car-hanging-fit-men-boy-keys', 'https://widget.alireviews.io/widget/review-widget?shop_id=36179214380&widget_id=147948&type_page=product&product_id=6807811489836&isAdminLogin=false&star=all&customer_id=&product_in_cart=&num_rand=0&total_order_values=0&avg_order_value=0&tag=&country=&last_purchase=&t=1647943423'],
            ['https://rv-test-1.myshopify.com/products/aachoae-women-elegant-long-wool-coat-with-belt-solid-color-long-sleeve-chic-outerwear-ladies-drop-shoulder-overcoat-2021', 'https://widget.alireviews.io/widget/review-widget?shop_id=36179214380&widget_id=147948&type_page=product&product_id=6807065755692&isAdminLogin=false&star=all&customer_id=&product_in_cart=&num_rand=0&total_order_values=0&avg_order_value=0&tag=&country=&last_purchase=&t=1647943423'],
        ];
    }
}
