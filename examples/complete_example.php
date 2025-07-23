<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DNSApi\DNSApiClient;
use DNSApi\DNSApiException;

/**
 * Complete DNS Management Example
 * 
 * This example shows a complete workflow for managing DNS with the IONOS API:
 * 1. Setting up the client
 * 2. Managing zones and records
 * 3. Dynamic DNS configuration
 * 4. Error handling
 */

// Configuration
$apiKey = 'your-ionos-api-key-here'; // Replace with your actual API key
$testDomain = 'example.com'; // Replace with your actual domain

try {
    echo "=== IONOS DNS API Complete Example ===\n\n";
    
    // Initialize the DNS client
    $dnsClient = new DNSApiClient($apiKey);
    echo "✓ DNS API Client initialized\n\n";
    
    // 1. GET ALL ZONES
    echo "1. Retrieving all DNS zones...\n";
    $zones = $dnsClient->getZones();
    echo "Found " . count($zones) . " zones:\n";
    
    foreach ($zones as $zone) {
        echo "  - {$zone->name} (ID: {$zone->id}, Type: {$zone->type})\n";
    }
    echo "\n";
    
    // Find a zone to work with
    $workingZone = null;
    foreach ($zones as $zone) {
        if (strpos($zone->name, $testDomain) !== false) {
            $workingZone = $zone;
            break;
        }
    }
    
    if (!$workingZone && !empty($zones)) {
        $workingZone = $zones[0]; // Use first available zone
    }
    
    if ($workingZone) {
        echo "2. Working with zone: {$workingZone->name}\n";
        
        // Get detailed zone information
        $zoneDetails = $dnsClient->getZone($workingZone->id);
        echo "Zone has " . count($zoneDetails->records) . " DNS records\n\n";
        
        // Display existing records
        echo "Existing records:\n";
        foreach ($zoneDetails->records as $record) {
            echo "  - {$record->name} ({$record->type}) -> {$record->content} [TTL: {$record->ttl}]\n";
        }
        echo "\n";
        
        // 3. CREATE NEW RECORDS
        echo "3. Creating new DNS records...\n";
        
        $newRecords = [
            $dnsClient->createRecord('api.' . $workingZone->name, 'A', '203.0.113.10', 3600),
            $dnsClient->createRecord('test.' . $workingZone->name, 'A', '203.0.113.11', 1800),
            $dnsClient->createRecord('txt-test.' . $workingZone->name, 'TXT', 'v=test123', 3600),
        ];
        
        echo "Prepared " . count($newRecords) . " records for creation:\n";
        foreach ($newRecords as $record) {
            echo "  - {$record->name} ({$record->type}) -> {$record->content}\n";
        }
        
        // Note: Uncomment the following lines to actually create the records
        // echo "\nCreating records...\n";
        // $createdRecords = $dnsClient->createRecords($workingZone->id, $newRecords);
        // echo "✓ Created " . count($createdRecords) . " new records\n\n";
        
        echo "\n[DEMO MODE] Records would be created here in production\n\n";
        
        // 4. RECORD FILTERING
        echo "4. Filtering records by type...\n";
        
        // Get only A records
        $aRecordsZone = $dnsClient->getZone($workingZone->id, ['recordType' => 'A']);
        echo "Found " . count($aRecordsZone->records) . " A records\n";
        
        // Get records by name pattern
        $wwwRecordsZone = $dnsClient->getZone($workingZone->id, ['recordName' => 'www']);
        echo "Found " . count($wwwRecordsZone->records) . " records matching 'www'\n\n";
        
        // 5. DYNAMIC DNS SETUP
        echo "5. Dynamic DNS configuration...\n";
        
        $domains = [$workingZone->name, 'www.' . $workingZone->name];
        echo "Would configure Dynamic DNS for: " . implode(', ', $domains) . "\n";
        
        // Note: Uncomment to actually set up Dynamic DNS
        // $dynamicDns = $dnsClient->activateDynamicDNS($domains, 'API Demo Dynamic DNS');
        // echo "✓ Dynamic DNS activated\n";
        // echo "Update URL: {$dynamicDns->updateUrl}\n";
        // echo "Bulk ID: {$dynamicDns->bulkId}\n\n";
        
        echo "[DEMO MODE] Dynamic DNS would be configured here\n\n";
        
    } else {
        echo "No zones found. Please ensure you have zones configured in your IONOS account.\n\n";
    }
    
    // 6. UTILITY INFORMATION
    echo "6. API Capabilities:\n";
    echo "Supported record types: " . implode(', ', $dnsClient->getSupportedRecordTypes()) . "\n";
    echo "Supported zone types: " . implode(', ', $dnsClient->getSupportedZoneTypes()) . "\n\n";
    
    // 7. BULK OPERATIONS EXAMPLE
    echo "7. Bulk operations example:\n";
    
    // Example of updating multiple records at once
    $bulkRecords = [
        $dnsClient->createRecord('cdn.' . ($workingZone ? $workingZone->name : 'example.com'), 'CNAME', 'cdn.provider.com'),
        $dnsClient->createRecord('mail.' . ($workingZone ? $workingZone->name : 'example.com'), 'A', '203.0.113.20'),
        $dnsClient->createRecord('backup.' . ($workingZone ? $workingZone->name : 'example.com'), 'A', '203.0.113.21'),
    ];
    
    echo "Bulk update would include " . count($bulkRecords) . " records:\n";
    foreach ($bulkRecords as $record) {
        echo "  - {$record->name} ({$record->type}) -> {$record->content}\n";
    }
    
    // Note: Use updateZone() to replace all records, or patchZone() to merge
    // $dnsClient->patchZone($workingZone->id, $bulkRecords);
    
    echo "\n=== Demo completed successfully! ===\n";
    echo "\nTo use this with real data:\n";
    echo "1. Replace 'your-ionos-api-key-here' with your actual API key\n";
    echo "2. Uncomment the actual API calls (marked with // comments)\n";
    echo "3. Replace 'example.com' with your actual domain\n";
    echo "4. Test with caution in a development environment first\n\n";
    
} catch (DNSApiException $e) {
    echo "❌ DNS API Error: " . $e->getMessage() . "\n";
    echo "HTTP Status Code: " . $e->getCode() . "\n";
    
    // Display detailed error information if available
    if ($e->getErrors()) {
        echo "\nDetailed error information:\n";
        foreach ($e->getErrors() as $error) {
            if (is_array($error)) {
                echo "  - " . ($error['message'] ?? 'Unknown error') . "\n";
                if (isset($error['code'])) {
                    echo "    Code: " . $error['code'] . "\n";
                }
            }
        }
    }
    
    echo "\nCommon solutions:\n";
    echo "- Verify your API key is correct\n";
    echo "- Check that you have the necessary permissions\n";
    echo "- Ensure the zone/record IDs exist\n";
    echo "- Review the IONOS API documentation\n";
    
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
    echo "This might be a network issue or configuration problem.\n";
    
    // Display stack trace in development
    if (defined('DEBUG') && DEBUG) {
        echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    }
}
