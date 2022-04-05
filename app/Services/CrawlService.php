<?php
namespace App\Services;

interface CrawlService
{
    public function crawlData($urlProduct, $originalProductId, $productId);

    // public function checkAppInstalled($shopName);

    public function checkAppInstalled($urlProductDefault);

}