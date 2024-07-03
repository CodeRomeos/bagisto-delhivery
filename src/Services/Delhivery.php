<?php

namespace CodeRomeos\BagistoDelhivery\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Delhivery
{

    protected $baseUrl;

    protected $token;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = config('delhivery.baseUrl');
        $this->token = config('delhivery.token');
    }

    public function trackAWB($awb)
    {
        $token = $this->token;

        if (!$token) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $token
        ])->get($this->baseUrl . '/packages/json/?waybill=' . $awb . '&token=' . $token);

        if ($response->failed()) {
            return false;
        }

        $json = $response->json();
        return $json;
    }

    public function getEstimatedDelivery(Request $request)
    {
        $token = $this->token;

        if (!$token) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $token
        ])->get($this->baseUrl . '/courier/serviceability', $request->all());
        if ($response->failed()) {
            return false;
        }

        $json = $response->json();
        return $json;
    }
}
