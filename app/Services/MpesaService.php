<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = config('mpesa.env') === 'sandbox' 
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    public function getAccessToken()
    {
        $consumerKey = config('mpesa.consumer_key');
        $consumerSecret = config('mpesa.consumer_secret');
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

        try {
            $response = $this->client->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $credentials
                ]
            ]);
            
            $body = json_decode($response->getBody()->getContents());
            return $body->access_token;
        } catch (\Exception $e) {
            Log::error('Mpesa Access Token Error: ' . $e->getMessage());
            return null;
        }
    }

    public function stkPush($phone, $amount, $accountReference, $transactionDesc)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            throw new \Exception("Failed to get Mpesa Access Token");
        }

        $shortcode = config('mpesa.shortcode');
        $passkey = config('mpesa.passkey');
        $timestamp = date('YmdHis');
        $password = base64_encode($shortcode . $passkey . $timestamp);

        // Format phone number to 254...
        $phone = preg_replace('/^0/', '254', $phone);

        try {
            $response = $this->client->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'BusinessShortCode' => $shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => round($amount),
                    'PartyA' => $phone,
                    'PartyB' => $shortcode,
                    'PhoneNumber' => $phone,
                    'CallBackURL' => config('mpesa.callback_url'),
                    'AccountReference' => $accountReference,
                    'TransactionDesc' => $transactionDesc
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            Log::error('Mpesa STK Push Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
