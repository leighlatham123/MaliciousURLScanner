<?php

namespace App\Http\Controllers\Providers;

use App\Services\ApiService;
use App\Http\Controllers\GlobalHandlerController;

class UrlScanioController extends GlobalHandlerController
{
    public $data;
    public $api_key;

    public function __construct()
    {
        $this->api_key = config('app.url_scan_io_key');
        $this->data = GlobalHandlerController::getAddress();
    }

    public function prepareUrlScan()
    {
        $result = (new IOScan())->scanUrl($this->api_key, $this->data);

        return (new IOResult())->getResult(json_decode($result, true));
    }
}

class IOScan
{
    public $url;

    public function __construct()
    {
        $this->url = 'https://urlscan.io/api/v1/scan/';
    }

    public function scanUrl($api, $data)
    {
        return (new ApiService())->postIO($this->url, $api, $data);
    }
}

class IOResult
{
    public $url;
    public $tries;
    public $processed;

    public function __construct()
    {
        $this->tries = 1;
        $this->processed = false;
    }

    public function getResult($result)
    {
        if(!array_key_exists('api', $result)) {
            return $result;
        }

        $url = $result['api'];

        do
        {
            $result = (new ApiService())->get($url);
            $result = json_decode($result, true);

            if (array_key_exists('status', $result))
            {
                if ($result['status'] != 200)
                {
                    if ($this->tries = 1)
                    {
                        sleep(10);
                    }
                    else
                    {
                        sleep(5);
                    }

                    $this->tries++;
                }
            }
            else
            {
                $this->processed = true;
                return $result;
            }


        } while (!$this->processed || $this->tries <= 30);

        return false;
    }
}
