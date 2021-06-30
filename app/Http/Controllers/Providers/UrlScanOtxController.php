<?php

namespace App\Http\Controllers\Providers;

use App\Services\ApiService;
use App\Http\Controllers\GlobalHandlerController;

class UrlScanOtxController extends GlobalHandlerController
{
    public $data;
    public $api_key;

    public function __construct()
    {
        $this->api_key = config('app.url_scan_otx_key');
        $this->data = GlobalHandlerController::getAddress();
    }

    public function prepareUrlScan()
    {
        return (new Scan())->scanUrl($this->api_key, $this->data);
    }
}

class Scan
{
    public $url;

    public function __construct()
    {
        $this->url = 'https://otx.alienvault.com/api/v1/indicators/submit_url';
    }

    public function scanUrl($api, $data)
    {
        return (new ApiService())->post($this->url, $api, $data);
    }
}
