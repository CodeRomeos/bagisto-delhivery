<?php

namespace CodeRomeos\BagistoDelhivery\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Delhivery
{

    protected $baseUrl;

    protected $userEmail;

    protected $userPassword;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = config('delhivery.baseUrl');
        $this->userEmail = config('delhivery.userEmail');
        $this->userPassword = config('delhivery.userPassword');

        $this->cacheToken();
    }


    public function generateToken()
    {
        $response = Http::post($this->baseUrl . '/auth/login', [
            'email' => $this->userEmail,
            'password' => $this->userPassword,
        ]);

        if ($response->failed()) {
            return false;
        }

        $json = $response->json();

        return isset($json['token']) ? $json['token'] : false;
    }

    function cacheToken()
    {
        $token = Cache::get('bagistodelhivery_token');

        if ($token) return $token;

        if ($token = $this->generateToken()) {
            Cache::put('bagistodelhivery_token', $token, 60 * 24 * 8);
            return $token;
        }

        return false;
    }

    public function trackAWB($awb)
    {
        $token = Cache::get('bagistodelhivery_token');

        if (!$token) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($this->baseUrl . '/courier/track/awb/' . $awb);
        if ($response->failed()) {
            return false;
        }

        $json = $response->json();
        return $json;
    }

    public function getEstimatedDelivery(Request $request)
    {
        $token = Cache::get('bagistodelhivery_token');

        if (!$token) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get($this->baseUrl . '/courier/serviceability', $request->all());
        if ($response->failed()) {
            return false;
        }

        $json = $response->json();
        return $json;
    }
}
