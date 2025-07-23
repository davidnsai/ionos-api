# Installation Guide

## Quick Installation

### Via Composer (Recommended)

1. **Install via Composer:**
   ```bash
   composer require davidnsai/ionos-dns-api-client
   ```

2. **Include the autoloader:**
   ```php
   <?php
   require_once 'vendor/autoload.php';
   use DNSApi\DNSApiClient;
   ```

3. **Initialize and use:**
   ```php
   $client = new DNSApiClient('your-api-key');
   $zones = $client->getZones();
   ```

### Manual Installation

1. **Download the source code** or clone this repository
2. **Include the files manually:**
   ```php
   <?php
   require_once 'src/DNSApiException.php';
   require_once 'src/HttpClientInterface.php';
   require_once 'src/CurlHttpClient.php';
   require_once 'src/DNSRecord.php';
   require_once 'src/DNSZone.php';
   require_once 'src/DynamicDNS.php';
   require_once 'src/DNSApiClient.php';
   
   use DNSApi\DNSApiClient;
   ```

## Requirements

- **PHP 7.4** or higher
- **cURL extension** (usually enabled by default)
- **JSON extension** (usually enabled by default)

### Check Requirements

```bash
php -m | grep curl
php -m | grep json
php -v
```

## Getting Your API Key

1. **Log into your IONOS account**
2. **Navigate to the API section**
3. **Generate a new API key** for DNS management
4. **Copy the API key** and store it securely

⚠️ **Important:** Never commit your API key to version control!

## Basic Configuration

### Environment Variables (Recommended)

Create a `.env` file:
```
IONOS_API_KEY=your_actual_api_key_here
```

Use in your code:
```php
$apiKey = $_ENV['IONOS_API_KEY'] ?? getenv('IONOS_API_KEY');
$client = new DNSApiClient($apiKey);
```

### Configuration File

Create a `config.php`:
```php
<?php
return [
    'ionos' => [
        'api_key' => 'your_actual_api_key_here',
        'base_url' => 'https://api.hosting.ionos.com/dns', // Optional
    ]
];
```

Use in your code:
```php
$config = require 'config.php';
$client = new DNSApiClient($config['ionos']['api_key']);
```

## Development Setup

For development and testing:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/davidnsai/ionos-api.git
   cd ionos-dns-api-client
   ```

2. **Install development dependencies:**
   ```bash
   composer install
   ```

3. **Run tests:**
   ```bash
   composer test
   ```

4. **Check code style:**
   ```bash
   composer cs-check
   ```

5. **Fix code style:**
   ```bash
   composer cs-fix
   ```

## Examples

Check the `examples/` directory for:
- `basic_usage.php` - Simple API usage
- `advanced_usage.php` - Advanced features and custom HTTP clients
- `complete_example.php` - Full workflow demonstration

## Troubleshooting

### Common Issues

**"Class not found" errors:**
- Ensure Composer autoloader is included: `require_once 'vendor/autoload.php';`
- Check that the package is properly installed: `composer show ionos/dns-api-client`

**cURL errors:**
- Verify cURL is installed: `php -m | grep curl`
- Check network connectivity and firewall settings
- Ensure SSL certificates are up to date

**API authentication errors:**
- Verify your API key is correct
- Check that the API key has DNS management permissions
- Ensure you're using the correct IONOS account

**HTTP timeout errors:**
- The default timeout is 30 seconds
- For slow connections, implement a custom HTTP client with longer timeouts

### Debug Mode

Enable error reporting for debugging:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $client = new DNSApiClient($apiKey);
    $zones = $client->getZones();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
```

## Next Steps

1. **Read the full documentation** in `README.md`
2. **Try the examples** in the `examples/` directory
3. **Check the API reference** for all available methods
4. **Review security considerations** in `SECURITY.md`

## Support

- **GitHub Issues:** [Report bugs and request features](https://github.com/davidnsai/ionos-api/issues)
- **Documentation:** [IONOS API Docs](https://developer.hosting.ionos.com/docs/dns)
- **Examples:** Check the `examples/` directory
