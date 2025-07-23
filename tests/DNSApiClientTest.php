<?php

namespace DNSApi\Tests;

use DNSApi\DNSApiClient;
use DNSApi\DNSApiException;
use DNSApi\DNSRecord;
use DNSApi\DNSZone;
use DNSApi\DynamicDNS;
use DNSApi\HttpClientInterface;
use PHPUnit\Framework\TestCase;

class DNSApiClientTest extends TestCase
{
    private $mockHttpClient;
    private $dnsClient;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->dnsClient = new DNSApiClient('test-api-key', 'https://api.test.com/dns', $this->mockHttpClient);
    }

    public function testConstructor()
    {
        $client = new DNSApiClient('test-key');
        $this->assertInstanceOf(DNSApiClient::class, $client);
    }

    public function testGetZones()
    {
        $responseData = [
            ['id' => 'zone1', 'name' => 'example.com', 'type' => 'NATIVE'],
            ['id' => 'zone2', 'name' => 'test.com', 'type' => 'NATIVE']
        ];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.test.com/dns/v1/zones', $this->anything())
            ->willReturn(['status_code' => 200, 'body' => json_encode($responseData)]);

        $zones = $this->dnsClient->getZones();

        $this->assertIsArray($zones);
        $this->assertCount(2, $zones);
        $this->assertInstanceOf(DNSZone::class, $zones[0]);
        $this->assertEquals('example.com', $zones[0]->name);
    }

    public function testGetZone()
    {
        $responseData = [
            'id' => 'zone1',
            'name' => 'example.com',
            'type' => 'NATIVE',
            'records' => [
                ['id' => 'rec1', 'name' => 'www.example.com', 'type' => 'A', 'content' => '192.168.1.1']
            ]
        ];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.test.com/dns/v1/zones/zone1', $this->anything())
            ->willReturn(['status_code' => 200, 'body' => json_encode($responseData)]);

        $zone = $this->dnsClient->getZone('zone1');

        $this->assertInstanceOf(DNSZone::class, $zone);
        $this->assertEquals('example.com', $zone->name);
        $this->assertCount(1, $zone->records);
        $this->assertInstanceOf(DNSRecord::class, $zone->records[0]);
    }

    public function testCreateRecord()
    {
        $record = $this->dnsClient->createRecord('www.example.com', 'A', '192.168.1.1', 3600);

        $this->assertInstanceOf(DNSRecord::class, $record);
        $this->assertEquals('www.example.com', $record->name);
        $this->assertEquals('A', $record->type);
        $this->assertEquals('192.168.1.1', $record->content);
        $this->assertEquals(3600, $record->ttl);
    }

    public function testCreateRecordInvalidType()
    {
        $this->expectException(DNSApiException::class);
        $this->expectExceptionMessage('Unsupported record type: INVALID');

        $this->dnsClient->createRecord('www.example.com', 'INVALID', '192.168.1.1');
    }

    public function testCreateRecords()
    {
        $requestData = [
            ['name' => 'www.example.com', 'type' => 'A', 'content' => '192.168.1.1', 'ttl' => 3600, 'disabled' => false]
        ];

        $responseData = [
            ['id' => 'rec1', 'name' => 'www.example.com', 'type' => 'A', 'content' => '192.168.1.1', 'ttl' => 3600]
        ];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'https://api.test.com/dns/v1/zones/zone1/records', [
                'headers' => [
                    'X-API-Key: test-api-key',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'body' => json_encode($requestData)
            ])
            ->willReturn(['status_code' => 200, 'body' => json_encode($responseData)]);

        $record = $this->dnsClient->createRecord('www.example.com', 'A', '192.168.1.1');
        $createdRecords = $this->dnsClient->createRecords('zone1', [$record]);

        $this->assertIsArray($createdRecords);
        $this->assertCount(1, $createdRecords);
        $this->assertInstanceOf(DNSRecord::class, $createdRecords[0]);
        $this->assertEquals('rec1', $createdRecords[0]->id);
    }

    public function testActivateDynamicDNS()
    {
        $requestData = [
            'domains' => ['example.com', 'www.example.com'],
            'description' => 'Test Dynamic DNS'
        ];

        $responseData = [
            'bulkId' => 'bulk123',
            'updateUrl' => 'https://ipv4.api.hosting.ionos.com/dns/v1/dyndns?q=bulk123',
            'domains' => ['example.com', 'www.example.com'],
            'description' => 'Test Dynamic DNS'
        ];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'https://api.test.com/dns/v1/dyndns', [
                'headers' => [
                    'X-API-Key: test-api-key',
                    'Content-Type: application/json',
                    'Accept: application/json'
                ],
                'body' => json_encode($requestData)
            ])
            ->willReturn(['status_code' => 200, 'body' => json_encode($responseData)]);

        $dynamicDns = $this->dnsClient->activateDynamicDNS(['example.com', 'www.example.com'], 'Test Dynamic DNS');

        $this->assertInstanceOf(DynamicDNS::class, $dynamicDns);
        $this->assertEquals('bulk123', $dynamicDns->bulkId);
        $this->assertContains('example.com', $dynamicDns->domains);
    }

    public function testApiError()
    {
        $errorResponse = [
            ['message' => 'Invalid API key', 'code' => 'INVALID_API_KEY']
        ];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn(['status_code' => 401, 'body' => json_encode($errorResponse)]);

        $this->expectException(DNSApiException::class);
        $this->expectExceptionMessage('Invalid API key');
        $this->expectExceptionCode(401);

        $this->dnsClient->getZones();
    }

    public function testGetSupportedRecordTypes()
    {
        $types = $this->dnsClient->getSupportedRecordTypes();
        
        $this->assertIsArray($types);
        $this->assertContains('A', $types);
        $this->assertContains('AAAA', $types);
        $this->assertContains('CNAME', $types);
        $this->assertContains('MX', $types);
    }

    public function testGetSupportedZoneTypes()
    {
        $types = $this->dnsClient->getSupportedZoneTypes();
        
        $this->assertIsArray($types);
        $this->assertContains('NATIVE', $types);
        $this->assertContains('SLAVE', $types);
    }
}
