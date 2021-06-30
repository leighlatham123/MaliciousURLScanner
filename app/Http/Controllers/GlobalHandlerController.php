<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Controllers\Providers\UrlScanioController;
use App\Http\Controllers\Providers\UrlGoogleSafeController;
use App\Http\Controllers\Providers\UrlVirusTotalController;

class GlobalHandlerController extends Controller
{
    protected static $address;

    public function __construct(SearchRequest $request)
    {
        self::$address = $request->address;
    }

    public function searchGoogleSafe() {
        return $this->executeUrlGoogleSafe(self::$address);
    }

    private function executeUrlGoogleSafe($address)
    {
        return (new UrlGoogleSafeController())->prepareUrlScan($address);
    }

    public function searchScanIO() {
        return $this->executeUrlScanIo(self::$address);
    }

    private function executeUrlScanIo($address)
    {
        return (new UrlScanioController())->prepareUrlScan($address);
    }

    public function searchVirusTotal() {
        return $this->executeUrlVirsuTotal(self::$address);
    }

    private function executeUrlVirsuTotal($address)
    {
        return (new UrlVirusTotalController())->prepareUrlScan($address);
    }

    public static function getAddress()
    {
        return self::$address;
    }
}
