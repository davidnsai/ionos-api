<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DNSApi\DNSApiClient;
use DNSApi\DNSApiException;

/**
 * Basic Usage Example
 * 
 * This example demonstrates the basic functionality of the IONOS DNS API Client.
 * Replace 'your-api-key-here' with your actual IONOS API key.
 */

try {
    // Initialize the client
    $dnsClient = new DNSApiClient('your-api-key-here');
    
    echo "=== IONOS DNS API Client Example ===\n\n";
    
    // Get all zones
    echo "1. Getting all zones...\n";
    $zones = $dnsClient->getZones();
    echo "Found " . count($zones) . " zones\n\n";
    
    if (!empty($zones)) {
        $zone = $zones[0];
        echo "First zone: {$zone->name} (ID: {$zone->id})\n\n";
        
        // Get specific zone with records
        echo "2. Getting zone details...\n";
        $zoneDetails = $dnsClient->getZone($zone->id);
        echo "Zone {$zoneDetails->name} has " . count($zoneDetails->records) . " records\n\n";
        
        // Create a new A record
        echo "3. Creating a new A record...\n";
        $record = $dnsClient->createRecord('test.example.com', 'A', '192.168.1.1', 3600);
        echo "Created record: {$record->name} -> {$record->content}\n\n";
        
        // Note: Uncomment the following lines to actually create the record
        // $createdRecords = $dnsClient->createRecords($zone->id, [$record]);
        // echo "Record created with ID: " . $createdRecords[0]->id . "\n\n";
    }
    
    // Show supported record types
    echo "4. Supported record types:\n";
    $recordTypes = $dnsClient->getSupportedRecordTypes();
    echo implode(', ', $recordTypes) . "\n\n";
    
    // Example of Dynamic DNS setup
    echo "5. Dynamic DNS example:\n";
    echo "To activate Dynamic DNS for domains:\n";
    echo "\$dynamicDns = \$dnsClient->activateDynamicDNS(['example.com', 'www.example.com'], 'My Dynamic DNS');\n";
    echo "This would return an update URL for dynamic IP updates.\n\n";
    
} catch (DNSApiException $e) {
    echo "DNS API Error: " . $e->getMessage() . "\n";
    echo "HTTP Code: " . $e->getCode() . "\n";
    
    if ($e->getErrors()) {
        echo "Error details:\n";
        print_r($e->getErrors());
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
