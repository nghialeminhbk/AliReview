<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\CrawlController;

class CrawControllerTest extends TestCase
{
    // /**
    //  * @param array $infoShop
    //  * @param bool $expectedResult
    //  * 
    //  * @dataProvider providerTestCheckValidToken
    //  */
    // public function testCheckValidToken($infoShop, $expectedResult){
    //     $crawController = new CrawlController();
    //     $result = $crawController->checkValidToken($infoShop['shopName'], $infoShop['accessToken']);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function providerTestCheckValidToken(){
    //     return [
    //         [['shopName' => 'nghialm', 'accessToken' => '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'], true],
    //         [['shopName' => 'rv-test-1', 'accessToken' => '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'], true],
    //         [['shopName' => 'rv-test-2', 'accessToken' => 'shpat_17ab166f1c41bd0d73c29cfdb40e673'], false]
    //     ];
    // }
}
