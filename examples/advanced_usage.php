<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DNSApi\DNSApiClient;
use DNSApi\DNSApiException;

/**
 * Advanced Usage Example
 * 
 * This example demonstrates advanced features including:
 * - Custom HTTP client
 * - Bulk record operations
 * - Error handling
 * - Dynamic DNS management
 */

// Example custom HTTP client using Guzzle (if available)
class GuzzleHttpClient implements \DNSApi\HttpClientInterface
{
    private $client;
    
    public function __construct()
    {
        // Uncomment if you have Guzzle installed
        // $this->client = new \GuzzleHttp\Client();
    }
    
    public function request($method, $url, $options = [])
    {
        // Example Guzzle implementation
        // $response = $this->client->request($method, $url, [
        //     'headers' => $this->parseHeaders($options['headers'] ?? []),
        //     'body' => $options['body'] ?? null
        // ]);
        // 
        // return [
        //     'status_code' => $response->getStatusCode(),
        //     'body' => $response->getBody()->getContents()
        // ];
        
        // Fallback to cURL for this example
        $curlClient = new \DNSApi\CurlHttpClient();
        return $curlClient->request($method, $url, $options);
    }
    
    private function parseHeaders($headers)
    {
        $parsed = [];
        foreach ($headers as $header) {
            $parts = explode(': ', $header, 2);
            if (count($parts) === 2) {
                $parsed[$parts[0]] = $parts[1];
            }
        }
        return $parsed;
    }
}

try {
    // Initialize with custom HTTP client
    $httpClient = new GuzzleHttpClient();
    $dnsClient = new DNSApiClient('your-api-key-here', 'https://api.hosting.ionos.com/dns', $httpClient);
    
    echo "=== Advanced DNS API Usage ===\n\n";
    
    // Bulk record creation example
    echo "1. Bulk record operations...\n";
    $records = [
        $dnsClient->createRecord('www.example.com', 'A', '192.168.1.1'),
        $dnsClient->createRecord('mail.example.com', 'A', '192.168.1.2'),
        $dnsClient->createRecord('example.com', 'MX', 'mail.example.com', 3600, 10),
        $dnsClient->createRecord('_dmarc.example.com', 'TXT', 'v=DMARC1; p=none; rua=mailto:dmarc@example.com'),
    ];
    
    echo "Prepared " . count($records) . " records for creation\n\n";
    
    // Zone filtering example
    echo "2. Zone filtering...\n";
    $zones = $dnsClient->getZones();
    
    if (!empty($zones)) {
        $zoneId = $zones[0]->id;
        
        // Get only A records
        $filteredZone = $dnsClient->getZone($zoneId, [
            'recordType' => 'A'
        ]);
        
        echo "Zone {$filteredZone->name} has " . count($filteredZone->records) . " A records\n\n";
    }
    
    // Dynamic DNS advanced usage
    echo "3. Dynamic DNS management...\n";
    
    // This would activate Dynamic DNS
    // $dynamicDns = $dnsClient->activateDynamicDNS(
    //     ['example.com', 'www.example.com'],
    //     'Production Dynamic DNS Configuration'
    // );
    
    // echo "Dynamic DNS activated with bulk ID: " . $dynamicDns->bulkId . "\n";
    // echo "Update URL: " . $dynamicDns->updateUrl . "\n\n";
    
    // Update Dynamic DNS configuration
    // $dnsClient->updateDynamicDNS($dynamicDns->bulkId, ['newdomain.com'], 'Updated configuration');
    // echo "Dynamic DNS configuration updated\n\n";
    
    echo "Dynamic DNS operations would be performed here in a real scenario.\n\n";
    
    // Record management with error handling
    echo "4. Record management with detailed error handling...\n";
    
    if (!empty($zones)) {
        $zoneId = $zones[0]->id;
        
        try {
            // Attempt to get a non-existent record (will fail)
            $record = $dnsClient->getRecord($zoneId, 'non-existent-record-id');
        } catch (DNSApiException $e) {
            echo "Expected error caught: " . $e->getMessage() . " (HTTP " . $e->getCode() . ")\n";
            
            if ($e->getErrors()) {
                echo "API error details available\n";
            }
        }
    }
    
    echo "\n5. Utility methods...\n";
    echo "Supported record types: " . implode(', ', $dnsClient->getSupportedRecordTypes()) . "\n";
    echo "Supported zone types: " . implode(', ', $dnsClient->getSupportedZoneTypes()) . "\n";
    
} catch (DNSApiException $e) {
    echo "DNS API Error: " . $e->getMessage() . "\n";
    echo "HTTP Status: " . $e->getCode() . "\n";
    
    if ($e->getErrors()) {
        echo "Detailed errors:\n";
        print_r($e->getErrors());
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
