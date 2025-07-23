# IONOS DNS API Client

A PHP client library for the IONOS DNS API, providing easy management of DNS zones, records, and Dynamic DNS configurations.

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## Installation

Install the package via Composer:

```bash
composer require ionos/dns-api-client
```

## Requirements

- PHP 8.1 or higher
- cURL extension
- JSON extension

## Quick Start

```php
<?php
require_once 'vendor/autoload.php';

use DNSApi\DNSApiClient;
use DNSApi\DNSApiException;

// Initialize the client
$dnsClient = new DNSApiClient('your-api-key-here');

try {
    // Get all zones
    $zones = $dnsClient->getZones();
    echo "Found " . count($zones) . " zones\n";
    
    // Create a new A record
    $record = $dnsClient->createRecord('www.example.com', 'A', '192.168.1.1', 3600);
    $createdRecords = $dnsClient->createRecords('zone-id-here', [$record]);
    
} catch (DNSApiException $e) {
    echo "DNS API Error: " . $e->getMessage() . "\n";
}
```

## Features

- **Zone Management**: List, get, update, and patch DNS zones
- **Record Management**: Create, read, update, and delete DNS records
- **Dynamic DNS**: Activate, update, and manage Dynamic DNS configurations
- **Type Safety**: Built-in validation for DNS record types
- **Error Handling**: Comprehensive exception handling with detailed error information
- **PSR-4 Autoloading**: Follows PHP-FIG standards
- **Extensible**: Injectable HTTP client interface for custom implementations

## Supported DNS Record Types

The client supports all major DNS record types:

- A, AAAA, CNAME, MX, NS, SOA, SRV, TXT
- CAA, TLSA, SMIMEA, SSHFP, DS
- HTTPS, SVCB, CERT, URI, RP, LOC, OPENPGPKEY

## Usage Examples

### Zone Operations

```php
// Get all zones
$zones = $dnsClient->getZones();

// Get a specific zone with optional filters
$zone = $dnsClient->getZone('zone-id', [
    'recordType' => 'A',
    'recordName' => 'www'
]);

// Update entire zone (replaces all records)
$records = [
    $dnsClient->createRecord('www.example.com', 'A', '192.168.1.1'),
    $dnsClient->createRecord('mail.example.com', 'A', '192.168.1.2')
];
$dnsClient->updateZone('zone-id', $records);

// Patch zone (replaces records of same name and type)
$dnsClient->patchZone('zone-id', $records);
```

### Record Operations

```php
// Create multiple records
$records = [
    $dnsClient->createRecord('www.example.com', 'A', '192.168.1.1'),
    $dnsClient->createRecord('example.com', 'MX', 'mail.example.com', 3600, 10)
];
$createdRecords = $dnsClient->createRecords('zone-id', $records);

// Get a specific record
$record = $dnsClient->getRecord('zone-id', 'record-id');

// Update a record
$updatedRecord = $dnsClient->updateRecord('zone-id', 'record-id', [
    'content' => '192.168.1.100',
    'ttl' => 7200
]);

// Delete a record
$dnsClient->deleteRecord('zone-id', 'record-id');
```

### Dynamic DNS

```php
// Activate Dynamic DNS
$dynamicDns = $dnsClient->activateDynamicDNS(
    ['example.com', 'www.example.com'], 
    'My Dynamic DNS Configuration'
);
echo "Update URL: " . $dynamicDns->updateUrl . "\n";

// Update Dynamic DNS configuration
$dnsClient->updateDynamicDNS('bulk-id', ['newdomain.com'], 'Updated config');

// Delete specific Dynamic DNS configuration
$dnsClient->deleteDynamicDNS('bulk-id');

// Disable all Dynamic DNS
$dnsClient->disableDynamicDNS();
```

### Custom HTTP Client

You can inject your own HTTP client implementation:

```php
use DNSApi\HttpClientInterface;

class MyHttpClient implements HttpClientInterface
{
    public function request($method, $url, $options = [])
    {
        // Your custom HTTP implementation
        // Must return ['status_code' => int, 'body' => string]
    }
}

$dnsClient = new DNSApiClient('api-key', 'https://api.hosting.ionos.com/dns', new MyHttpClient());
```

## Error Handling

The client throws `DNSApiException` for API errors:

```php
try {
    $zones = $dnsClient->getZones();
} catch (DNSApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "HTTP Code: " . $e->getCode() . "\n";
    
    // Get detailed error information
    $errors = $e->getErrors();
    if ($errors) {
        print_r($errors);
    }
}
```

## Development

### Running Tests

```bash
composer test
```

### Code Style

Check code style:
```bash
composer cs-check
```

Fix code style:
```bash
composer cs-fix
```

### Static Analysis

```bash
composer analyse
```

## API Documentation

For complete API documentation, visit the [IONOS DNS API Documentation](https://developer.hosting.ionos.com/docs/dns).

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- Create an issue on [GitHub](https://github.com/davidnsai/ionos-api/issues)
- Check the [IONOS API Documentation](https://developer.hosting.ionos.com/docs/dns)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and releases.
