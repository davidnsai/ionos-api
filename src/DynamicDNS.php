<?php

namespace DNSApi;

/**
 * Dynamic DNS Configuration Model
 *
 * Represents a Dynamic DNS configuration with its properties.
 */
class DynamicDNS
{
    /**
     * @var string|null Bulk ID for the Dynamic DNS configuration
     */
    public $bulkId;

    /**
     * @var string|null Update URL for Dynamic DNS
     */
    public $updateUrl;

    /**
     * @var array List of domains configured for Dynamic DNS
     */
    public $domains = [];

    /**
     * @var string|null Description of the Dynamic DNS configuration
     */
    public $description;

    /**
     * Constructor
     *
     * @param array $data Dynamic DNS data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
