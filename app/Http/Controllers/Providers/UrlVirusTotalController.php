<?php

namespace App\Http\Controllers\Providers;

use App\Services\ApiService;
use App\Http\Controllers\GlobalHandlerController;

class UrlVirusTotalController extends GlobalHandlerController
{
    public $data;
    public $api_key;

    public function __construct()
    {
        $this->api_key = config('app.url_virus_total_key');
        $this->data = GlobalHandlerController::getAddress();
    }

    public function prepareUrlScan()
    {
        $result = (new VTScan())->scanUrl($this->api_key, $this->data);

        return (new VTResult())->getResult($result, $this->api_key);
    }
}

class VTScan
{
    public $url;

    public function __construct()
    {
        $this->url = 'https://www.virustotal.com/vtapi/v2/url/scan';
    }

    public function scanUrl($api, $data)
    {
        return (new ApiService())->postVT($this->url, $api, $data);
    }
}

class VTResult
{
    public $url;
    public $tries;
    public $processed;

    public function __construct()
    {
        $this->tries = 1;
        $this->processed = false;
        $this->url = 'https://www.virustotal.com/vtapi/v2/url/report?';
    }

    public function getResult($result, $apikey)
    {
        $result = json_decode($result['body'], true);
        $url = $this->url . 'apikey=' .$apikey. '&resource=' .$result['scan_id'];
        $result = (new ApiService())->get($url);

        return $this->processResult($result);

    }

    private function processResult($result)
    {
        if ($result)
        {
            return json_decode($result, true);
        }

        return $result;
    }
}
