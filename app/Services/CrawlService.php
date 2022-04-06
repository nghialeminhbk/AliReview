<?php
namespace App\Services;

interface CrawlService
{
    public function crawlData($urlProduct, $originalOriginalProductId, $productId);

    // public function checkAppInstalled($shopName);

    public function checkAppInstalled($urlProductDefault);

}
