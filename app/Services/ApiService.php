<?php

namespace App\Services;

use GuzzleHttp\Client;

class ApiService
{
    private static $client;

    public function __construct()
    {
        self::$client = new Client;
    }

    public function get($URI)
    {
        $response = self::$client->get($URI, [
            'http_errors' => false,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json'
            ],
        ]);

        return $response->getBody()->getContents();
    }

    public function postIO($URI, $API, $url)
    {
        $response = self::$client->post($URI, [
            'http_errors' => false,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'API-Key' => $API
            ],
            'json' => [
                'url' => $url,
                'visibility' => 'public'
             ]
        ]);

        if (!$response->getBody())
        {
            return null;
        }

        return $response->getBody()->getContents();
    }

    public function postOTX($URI, $API, $url)
    {
        $response = self::$client->post($URI, [
            'http_errors' => false,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'API' => $API
            ],
            'json' => [
                //"$schema" => 'http://json-schema.org/draft-04/schema',
                'additionalParameters' => false,
                'required' => $url,
                'parameters' => [
                    'url' => ['type' => 'string'],
                    'tlp' => [
                        'type' => 'string',
                        'enum' => [
                            'white', 'green', 'amber', 'red'
                        ]
                    ],
                ]
             ]
        ]);

        if (!$response->getBody())
        {
            return null;
        }

        return $response->getBody()->getContents();
    }

    public function postSafe($URI, $url)
    {
        $response = self::$client->post($URI, [
            'http_errors' => false,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'Referer' => config('app.url')
            ],
            'json' => [
                'client' => [
                    'clientId' => 'uTrack',
                    'clientVersion' => '1.5.2'
                ],
                'threatInfo' => [
                    'threatTypes' => [ 'MALWARE', 'SOCIAL_ENGINEERING' ],
                    'platformTypes' => [ 'WINDOWS' ],
                    'threatEntryTypes' => [ 'URL' ],
                    'threatEntries' => [
                        'url' => $url
                    ]
                ]
             ]
        ]);

        $status = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        if ($body = $response->getBody()) {
            return array(
                'status' => $status,
                'reason' => $reason,
                'body' => $body->getContents()
            );
        }

        return array(
            'status' => $status,
            'reason' => $reason
        );
    }

    public function postVT($URI, $API, $url)
    {
        $response = self::$client->post($URI, [
            'http_errors' => false,
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'apikey' => $API,
                'url' => $url
            ]
        ]);

        $status = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        if ($body = $response->getBody()) {
            return array(
                'status' => $status,
                'reason' => $reason,
                'body' => $body->getContents()
            );
        }

        return array(
            'status' => $status,
            'reason' => $reason
        );
    }
}
