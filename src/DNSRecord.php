<?php

namespace DNSApi;

/**
 * DNS Record Model
 *
 * Represents a DNS record with all its properties.
 * Provides methods for data conversion and manipulation.
 */
class DNSRecord
{
    /**
     * @var string|null Record ID
     */
    public $id;

    /**
     * @var string Record name/hostname
     */
    public $name;

    /**
     * @var string|null Root name
     */
    public $rootName;

    /**
     * @var string Record type (A, AAAA, CNAME, etc.)
     */
    public $type;

    /**
     * @var string Record content/value
     */
    public $content;

    /**
     * @var string|null Last change date
     */
    public $changeDate;

    /**
     * @var int Time to live in seconds
     */
    public $ttl;

    /**
     * @var int|null Priority (for MX, SRV records)
     */
    public $prio;

    /**
     * @var bool Whether the record is disabled
     */
    public $disabled;

    /**
     * Constructor
     *
     * @param array $data Record data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Convert record to array
     *
     * @return array Record data as associative array
     */
    public function toArray()
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }
        return $data;
    }
}
