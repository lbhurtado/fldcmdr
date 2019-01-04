<?php

namespace App\Services;

use Illuminate\Support\Arr;
use GuzzleHttp\Client as HttpClient;

class EngageSpark
{
    const FORMAT_JSON = 3;

    /** @var HttpClient */
    protected $client;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $api_key;

    /** @var string */
    protected $org_id;

    /** @var string */
    protected $recipient_type = 'mobile_number';

    /** @var string */
    protected $sender_id;

    public function __construct(array $config)
    {
        $this->endpoint 		= Arr::get($config, 'endpoint', 'https://start.engagespark.com/api/v1/messages/sms');
        $this->api_key 			= Arr::get($config, 'api_key');
        $this->org_id 			= Arr::get($config, 'org_id');
        // $this->recipient_type 	= Arr::get($config, 'recipient_type');
        // $this->sender_id 		= Arr::get($config, 'sender_id');

        $this->client = new HttpClient([
            // 'timeout' => 5,
            // 'connect_timeout' => 5,
        ]);
    }

    public function send($params)
    {
        $base = [
            // 'charset' => 'utf-8',
            'organization_id'   => $this->org_id,
            'recipient_type'    => $this->recipient_type,
            // 'sender_id'  		=> $this->sender_id,
            // 'fmt'     => self::FORMAT_JSON,
        ];

        $params = \array_merge($base, \array_filter($params));

        // dd($params);
        try {
            $response = $this->client->request('POST', $this->endpoint, [
	            'headers' => [
				    'Authorization' => 'Token ' . $this->api_key,        
				    'Accept'        => 'application/json',
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
}