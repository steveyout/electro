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
    public function getProducts($parameters)
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/products',$parameters); // Example: hitting the products endpoint

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
        ])->get($this->baseUrl.$this->prefix.'/products?featured=1?limit=12'); // Example: hitting the products endpoint

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
        ])->get($this->baseUrl.$this->prefix.'/v1/categories'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    // ///////////////////////
    // / get new products
    // / /////////////////
    public function getNewProducts()
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/products?sort=created_at?limit=12'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    // ///////////////////////
    // / get product by id
    // / /////////////////
    public function getProduct($id)
    {
        if (! $id) {
            // Handle the error
            throw new Exception('API request failed: no product id provided');
        }

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/v1/products/'.$id); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    // ///////////////////////
    // / get product reviews
    // / /////////////////
    public function getProductReviews($id)
    {
        if (! $id) {
            // Handle the error
            throw new Exception('API request failed: no product id provided');
        }

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/v1/products/'.$id.'/reviews'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    // ///////////////////////
    // / get product reviews
    // / /////////////////
    public function getRelatedProducts($id)
    {
        if (! $id) {
            // Handle the error
            throw new Exception('API request failed: no product id provided');
        }

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->get($this->baseUrl.$this->prefix.'/products/'.$id.'/related'); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }

    // ///////////////////////
    // / add to cart
    // / /////////////////
    public function addToCart($id)
    {
        if (! $id) {
            // Handle the error
            throw new Exception('API request failed: no product id provided');
        }

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->token, // For authenticated endpoints
        ])->post($this->baseUrl.$this->prefix.'/v1/customer/cart/add/'.$id); // Example: hitting the products endpoint

        if ($response->failed()) {
            // Handle the error
            throw new Exception('API request failed: '.$response->status());
        }

        return $response->json();
    }
}
