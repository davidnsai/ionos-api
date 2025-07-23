<?php

namespace DNSApi;

/**
 * Default cURL HTTP Client
 *
 * Default implementation of HttpClientInterface using cURL.
 * Provides basic HTTP functionality for the DNS API client.
 */
class CurlHttpClient implements HttpClientInterface
{
    /**
     * Make an HTTP request using cURL
     *
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param array $options Request options
     * @return array Response array with status_code and body
     * @throws DNSApiException On cURL error
     */
    public function request($method, $url, $options = [])
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['body']);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new DNSApiException("cURL Error: " . $error);
        }

        return [
            'status_code' => $httpCode,
            'body' => $response
        ];
    }
}
