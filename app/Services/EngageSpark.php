<?php

namespace App\Services;

use Illuminate\Support\Arr;
use GuzzleHttp\Client as HttpClient;

class EngageSpark
{
    const FORMAT_JSON = 3;

    /** @var HttpClient */
    protected $client;

    /** @var array */
    protected $end_points;

    /** @var string */
    protected $api_key;

    /** @var string */
    protected $org_id;

    /** @var string */
    protected $sender_id;

    /** @var array */
    protected $web_hooks;

    public function __construct(array $config)
    {
        $this->end_points = Arr::get($config, 'end_points');
        $this->api_key 	  = Arr::get($config, 'api_key');
        $this->org_id     = Arr::get($config, 'org_id');
        $this->client     = new HttpClient([
                                // 'timeout'         => 5,
                                // 'connect_timeout' => 5,
        ]);
        $this->web_hooks  = Arr::get($config, 'web_hooks');
    }

    public function send($params, $mode = 'sms')
    {
        $base = [
            'organization_id'   => $this->org_id,
        ];
        $params = \array_merge($base, \array_filter($params));

        try {
            $response = $this->client->request('POST', $this->getEndPoint($mode), [
	            'headers' => [
				    'Authorization' => 'Token ' . $this->api_key,        
				    'Accept' => 'application/json',
	            ],
            	'json' => $params
            ]);

            $response = \json_decode((string) $response->getBody(), true);

            // if (isset($response['error'])) {
            //     throw new \DomainException($response['error'], $response['error_code']);
            // }

            return $response;
        // } catch (\DomainException $exception) {
        //     throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            // throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
            throw $exception;
        }
    }

    protected function getEndPoint($mode)
    {
        return Arr::get($this->end_points, $mode, $this->end_points['sms']);
    }

    public function getWebHook($mode)
    {
         return Arr::get($this->web_hooks, $mode, $this->web_hooks['sms']);   
    }

    public function getApiKey()
    {
        return $this->api_key;
    }
}