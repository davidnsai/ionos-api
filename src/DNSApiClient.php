<?php

namespace DNSApi;

/**
 * Main DNS API Client
 *
 * Primary class for interacting with the IONOS DNS API.
 * Provides methods for managing DNS zones, records, and Dynamic DNS configurations.
 */
class DNSApiClient
{
    /**
     * @var string API key for authentication
     */
    private $apiKey;

    /**
     * @var string Base URL for the API
     */
    private $baseUrl;

    /**
     * @var HttpClientInterface HTTP client instance
     */
    private $httpClient;

    /**
     * Supported DNS record types
     */
    const SUPPORTED_RECORD_TYPES = [
        'A', 'AAAA', 'CNAME', 'MX', 'NS', 'SOA', 'SRV', 'TXT', 'CAA',
        'TLSA', 'SMIMEA', 'SSHFP', 'DS', 'HTTPS', 'SVCB', 'CERT',
        'URI', 'RP', 'LOC', 'OPENPGPKEY'
    ];

    /**
     * Supported zone types
     */
    const ZONE_TYPES = ['NATIVE', 'SLAVE'];

    /**
     * Constructor
     *
     * @param string $apiKey API key for authentication
     * @param string $baseUrl Base URL for the API
     * @param HttpClientInterface|null $httpClient Custom HTTP client (optional)
     */
    public function __construct($apiKey, $baseUrl = 'https://api.hosting.ionos.com/dns', HttpClientInterface $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = $httpClient ?: new CurlHttpClient();
    }

    /**
     * Make HTTP request to API
     *
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param mixed $data Request data
     * @param array $queryParams Query parameters
     * @return mixed API response data
     * @throws DNSApiException On API error
     */
    private function makeRequest($method, $endpoint, $data = null, $queryParams = [])
    {
        $url = $this->baseUrl . $endpoint;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $headers = [
            'X-API-Key: ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        $options = ['headers' => $headers];

        if ($data !== null) {
            $options['body'] = json_encode($data);
        }

        $response = $this->httpClient->request($method, $url, $options);

        return $this->handleResponse($response);
    }

    /**
     * Handle API response
     *
     * @param array $response HTTP response
     * @return mixed Decoded response data
     * @throws DNSApiException On API error
     */
    private function handleResponse($response)
    {
        $statusCode = $response['status_code'];
        $body = $response['body'];

        if ($statusCode >= 200 && $statusCode < 300) {
            return $body ? json_decode($body, true) : null;
        }

        $errorData = $body ? json_decode($body, true) : null;
        $errorMessage = 'API Error';

        if ($errorData && is_array($errorData)) {
            if (isset($errorData[0]['message'])) {
                $errorMessage = $errorData[0]['message'];
            }
        }

        throw new DNSApiException($errorMessage, $statusCode, $errorData);
    }

    // ZONE OPERATIONS

    /**
     * Get all customer zones
     *
     * @return DNSZone[] Array of DNS zones
     * @throws DNSApiException On API error
     */
    public function getZones()
    {
        $response = $this->makeRequest('GET', '/v1/zones');
        return array_map(function ($zone) {
            return new DNSZone($zone);
        }, $response ?: []);
    }

    /**
     * Get a specific zone by ID
     *
     * @param string $zoneId Zone ID
     * @param array $filters Optional filters (suffix, recordName, recordType)
     * @return DNSZone Zone data
     * @throws DNSApiException On API error
     */
    public function getZone($zoneId, $filters = [])
    {
        $queryParams = [];

        if (isset($filters['suffix'])) {
            $queryParams['suffix'] = $filters['suffix'];
        }
        if (isset($filters['recordName'])) {
            $queryParams['recordName'] = $filters['recordName'];
        }
        if (isset($filters['recordType'])) {
            $queryParams['recordType'] = $filters['recordType'];
        }

        $response = $this->makeRequest('GET', "/v1/zones/{$zoneId}", null, $queryParams);
        return new DNSZone($response);
    }

    /**
     * Update entire zone (replaces all records)
     *
     * @param string $zoneId Zone ID
     * @param array $records Array of DNSRecord objects or arrays
     * @return bool Success status
     * @throws DNSApiException On API error
     */
    public function updateZone($zoneId, array $records)
    {
        $recordData = array_map(function ($record) {
            return $record instanceof DNSRecord ? $record->toArray() : $record;
        }, $records);

        $this->makeRequest('PUT', "/v1/zones/{$zoneId}", $recordData);
        return true;
    }

    /**
     * Patch zone (replaces records of same name and type)
     *
     * @param string $zoneId Zone ID
     * @param array $records Array of DNSRecord objects or arrays
     * @return bool Success status
     * @throws DNSApiException On API error
     */
    public function patchZone($zoneId, array $records)
    {
        $recordData = array_map(function ($record) {
            return $record instanceof DNSRecord ? $record->toArray() : $record;
        }, $records);

        $this->makeRequest('PATCH', "/v1/zones/{$zoneId}", $recordData);
        return true;
    }

    // RECORD OPERATIONS

    /**
     * Create records in a zone
     *
     * @param string $zoneId Zone ID
     * @param array $records Array of DNSRecord objects or arrays
     * @return DNSRecord[] Array of created records
     * @throws DNSApiException On API error
     */
    public function createRecords($zoneId, array $records)
    {
        $recordData = array_map(function ($record) {
            return $record instanceof DNSRecord ? $record->toArray() : $record;
        }, $records);

        $response = $this->makeRequest('POST', "/v1/zones/{$zoneId}/records", $recordData);
        return array_map(function ($record) {
            return new DNSRecord($record);
        }, $response ?: []);
    }

    /**
     * Get a specific record
     *
     * @param string $zoneId Zone ID
     * @param string $recordId Record ID
     * @return DNSRecord Record data
     * @throws DNSApiException On API error
     */
    public function getRecord($zoneId, $recordId)
    {
        $response = $this->makeRequest('GET', "/v1/zones/{$zoneId}/records/{$recordId}");
        return new DNSRecord($response);
    }

    /**
     * Update a specific record
     *
     * @param string $zoneId Zone ID
     * @param string $recordId Record ID
     * @param array $updateData Record update data
     * @return DNSRecord Updated record
     * @throws DNSApiException On API error
     */
    public function updateRecord($zoneId, $recordId, $updateData)
    {
        $response = $this->makeRequest('PUT', "/v1/zones/{$zoneId}/records/{$recordId}", $updateData);
        return new DNSRecord($response);
    }

    /**
     * Delete a specific record
     *
     * @param string $zoneId Zone ID
     * @param string $recordId Record ID
     * @return bool Success status
     * @throws DNSApiException On API error
     */
    public function deleteRecord($zoneId, $recordId)
    {
        $this->makeRequest('DELETE', "/v1/zones/{$zoneId}/records/{$recordId}");
        return true;
    }

    // DYNAMIC DNS OPERATIONS

    /**
     * Activate Dynamic DNS
     *
     * @param array $domains List of domains
     * @param string|null $description Optional description
     * @return DynamicDNS Dynamic DNS configuration
     * @throws DNSApiException On API error
     */
    public function activateDynamicDNS(array $domains, $description = null)
    {
        $data = ['domains' => $domains];
        if ($description !== null) {
            $data['description'] = $description;
        }

        $response = $this->makeRequest('POST', '/v1/dyndns', $data);
        return new DynamicDNS($response);
    }

    /**
     * Update Dynamic DNS configuration
     *
     * @param string $bulkId Bulk ID
     * @param array $domains List of domains
     * @param string|null $description Optional description
     * @return bool Success status
     * @throws DNSApiException On API error
     */
    public function updateDynamicDNS($bulkId, array $domains, $description = null)
    {
        $data = ['domains' => $domains];
        if ($description !== null) {
            $data['description'] = $description;
        }

        $this->makeRequest('PUT', "/v1/dyndns/{$bulkId}", $data);
        return true;
    }

    /**
     * Disable all Dynamic DNS
     *
     * @return bool Success status
     * @throws DNSApiException On API error
     */
    public function disableDynamicDNS()
    {
        $this->makeRequest('DELETE', '/v1/dyndns');
        return true;
    }

    /**
     * Delete specific Dynamic DNS configuration
     *
     * @param string $bulkId Bulk ID
     * @return bool Success status
     * @throws DNSApiException On API error
     */
    public function deleteDynamicDNS($bulkId)
    {
        $this->makeRequest('DELETE', "/v1/dyndns/{$bulkId}");
        return true;
    }

    // HELPER METHODS

    /**
     * Create a new DNS record object
     *
     * @param string $name Record name
     * @param string $type Record type
     * @param string $content Record content
     * @param int $ttl Time to live
     * @param int|null $prio Priority (for MX, SRV records)
     * @param bool $disabled Whether record is disabled
     * @return DNSRecord New DNS record
     * @throws DNSApiException If record type is not supported
     */
    public function createRecord($name, $type, $content, $ttl = 3600, $prio = null, $disabled = false)
    {
        if (!in_array($type, self::SUPPORTED_RECORD_TYPES)) {
            throw new DNSApiException("Unsupported record type: {$type}");
        }

        return new DNSRecord([
            'name' => $name,
            'type' => $type,
            'content' => $content,
            'ttl' => $ttl,
            'prio' => $prio,
            'disabled' => $disabled
        ]);
    }

    /**
     * Get supported record types
     *
     * @return array Supported DNS record types
     */
    public function getSupportedRecordTypes()
    {
        return self::SUPPORTED_RECORD_TYPES;
    }

    /**
     * Get supported zone types
     *
     * @return array Supported zone types
     */
    public function getSupportedZoneTypes()
    {
        return self::ZONE_TYPES;
    }
}
