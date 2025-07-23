<?php

namespace DNSApi;

/**
 * HTTP Client Interface
 *
 * Interface for HTTP client implementations used by the DNS API client.
 * Allows for custom HTTP client implementations (Guzzle, PSR-18, etc.)
 */
interface HttpClientInterface
{
    /**
     * Make an HTTP request
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE, PATCH)
     * @param string $url Full URL to request
     * @param array $options Request options (headers, body, etc.)
     * @return array Response with 'status_code' and 'body' keys
     * @throws DNSApiException On request failure
     */
    public function request($method, $url, $options = []);
}
