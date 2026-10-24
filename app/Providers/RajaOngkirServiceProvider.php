<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class RajaOngkirServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('rajaongkir', function ($app) {
            return new class {
                protected $apiKey;
                protected $baseUrl;
                protected $endpoints;
                protected $authType;
                protected $authHeader;
                protected $timeout;

                public function __construct()
                {
                    $this->apiKey = config('services.rajaongkir.key');
                    $this->baseUrl = rtrim(config('services.rajaongkir.base_url'), '/') . '/';
                    $this->endpoints = config('services.rajaongkir.endpoints', []);
                    $this->authType = config('services.rajaongkir.auth_type', 'header');
                    $this->authHeader = config('services.rajaongkir.auth_header', 'key');
                    $this->timeout = (int) config('services.rajaongkir.timeout', 15);
                }

                protected function http()
                {
                    $http = Http::timeout($this->timeout)->acceptJson();

                    if ($this->apiKey) {
                        if ($this->authType === 'bearer') {
                            $http = $http->withToken($this->apiKey);
                        } else {
                            $http = $http->withHeaders([
                                $this->authHeader => $this->apiKey
                            ]);
                        }
                    }

                    return $http;
                }

                protected function buildEndpoint(string $key): string
                {
                    $path = $this->endpoints[$key] ?? $key;

                    return $this->baseUrl . ltrim($path, '/');
                }

                public function provinces(array $params = [])
                {
                    return $this->http()->get(
                        $this->buildEndpoint('provinces'),
                        array_filter($params, fn($value) => $value !== null && $value !== '')
                    );
                }

                public function cities(array $params = [])
                {
                    $provinceId = $params['province_id'] ?? null;
                    unset($params['province_id']);

                    $endpoint = $this->buildEndpoint('cities');

                    if ($provinceId) {
                        $endpoint = rtrim($endpoint, '/') . '/' . $provinceId;
                    }

                    return $this->http()->get(
                        $endpoint,
                        array_filter($params, fn($value) => $value !== null && $value !== '')
                    );
                }

                public function districts(array $params = [])
                {
                    $cityId = $params['city_id'] ?? $params['city'] ?? null;
                    unset($params['city_id'], $params['city']);

                    $endpoint = $this->buildEndpoint('districts');

                    if ($cityId) {
                        $endpoint = rtrim($endpoint, '/') . '/' . $cityId;
                    }

                    return $this->http()->get(
                        $endpoint,
                        array_filter($params, fn($value) => $value !== null && $value !== '')
                    );
                }

                public function cost(array $payload)
                {
                    return $this->http()
                        ->asForm()
                        ->post(
                            $this->buildEndpoint('cost'),
                            array_filter($payload, fn($value) => $value !== null && $value !== '')
                        );
                }

                public function waybill(array $payload)
                {
                    return $this->http()
                        ->asForm()
                        ->post(
                            $this->buildEndpoint('waybill'),
                            array_filter($payload, fn($value) => $value !== null && $value !== '')
                        );
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
