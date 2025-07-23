<?php

namespace DNSApi;

/**
 * DNS Zone Model
 *
 * Represents a DNS zone with its properties and associated records.
 */
class DNSZone
{
    /**
     * @var string|null Zone ID
     */
    public $id;

    /**
     * @var string Zone name/domain
     */
    public $name;

    /**
     * @var string Zone type (NATIVE, SLAVE)
     */
    public $type;

    /**
     * @var DNSRecord[] Array of DNS records in this zone
     */
    public $records = [];

    /**
     * Constructor
     *
     * @param array $data Zone data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if ($key === 'records' && is_array($value)) {
                $this->records = array_map(function ($record) {
                    return new DNSRecord($record);
                }, $value);
            } elseif (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
