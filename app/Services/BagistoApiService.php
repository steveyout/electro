<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class BagistoApiService
{
    protected string $baseUrl;

    protected string $token;

    protected string $prefix;

    public function __construct()
    {
        $this->baseUrl = config('app.url');
        // You would typically retrieve the token from the session/cache after login
        $this->token = session('bagisto_api_token') ?? '';
        $this->prefix = '/api';
    }

    // /get all products
    public function getProducts()
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/products'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    // //get featured products
    public function getFeaturedProducts()
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/products?featured=1?limit=6'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }
    // //get categories
    public function getCategories()
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/categories'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    /////////////////////////
    /// get categories
}
