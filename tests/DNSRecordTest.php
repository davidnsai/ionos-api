<?php

namespace DNSApi\Tests;

use DNSApi\DNSRecord;
use PHPUnit\Framework\TestCase;

class DNSRecordTest extends TestCase
{
    public function testConstructor()
    {
        $data = [
            'id' => 'rec123',
            'name' => 'www.example.com',
            'type' => 'A',
            'content' => '192.168.1.1',
            'ttl' => 3600,
            'disabled' => false
        ];

        $record = new DNSRecord($data);

        $this->assertEquals('rec123', $record->id);
        $this->assertEquals('www.example.com', $record->name);
        $this->assertEquals('A', $record->type);
        $this->assertEquals('192.168.1.1', $record->content);
        $this->assertEquals(3600, $record->ttl);
        $this->assertFalse($record->disabled);
    }

    public function testToArray()
    {
        $record = new DNSRecord([
            'name' => 'test.example.com',
            'type' => 'A',
            'content' => '192.168.1.100',
            'ttl' => 7200
        ]);

        $array = $record->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('test.example.com', $array['name']);
        $this->assertEquals('A', $array['type']);
        $this->assertEquals('192.168.1.100', $array['content']);
        $this->assertEquals(7200, $array['ttl']);
        $this->assertArrayNotHasKey('id', $array); // Should not include null values
    }

    public function testToArrayExcludesNullValues()
    {
        $record = new DNSRecord([
            'name' => 'test.example.com',
            'type' => 'A',
            'content' => '192.168.1.1'
            // ttl, prio, etc. are null
        ]);

        $array = $record->toArray();

        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('ttl', $array);
        $this->assertArrayNotHasKey('prio', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('content', $array);
    }
}
