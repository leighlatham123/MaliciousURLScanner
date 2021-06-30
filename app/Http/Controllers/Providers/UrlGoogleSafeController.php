<?php

namespace App\Http\Controllers\Providers;

use App\Services\ApiService;
use App\Http\Controllers\GlobalHandlerController;

class UrlGoogleSafeController extends GlobalHandlerController
{
    public $data;
    public $api_key;

    public function __construct()
    {
        $this->api_key = config('app.url_google_safe_key');
        $this->data = GlobalHandlerController::getAddress();
    }

    public function prepareUrlScan()
    {
        return (new GoogleScan())->scanUrl($this->api_key, $this->data);
    }
}

class GoogleScan
{
    public $url;

    public function __construct()
    {
        $this->url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=';
    }

    public function scanUrl($api, $data)
    {
        $result = (new ApiService())->postSafe($this->url.$api, $data);

        return $this->processResult($result);
    }

    private function processResult($result)
    {
        if ($result['status'] == 200)
        {
            return json_decode($result['body'], true);
        }

        return $result;
    }
}
