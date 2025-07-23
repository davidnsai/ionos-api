# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-06-19

### Added
- Initial release of IONOS DNS API Client
- Complete DNS zone management (list, get, update, patch)
- DNS record operations (create, read, update, delete)
- Dynamic DNS configuration management
- Support for all major DNS record types
- Comprehensive error handling with DNSApiException
- PSR-4 autoloading
- Injectable HTTP client interface
- Built-in cURL HTTP client implementation
- Type-safe models for DNS records, zones, and Dynamic DNS
- Extensive documentation and usage examples

### Features
- **Zone Operations**: Full CRUD operations for DNS zones
- **Record Management**: Create, update, and delete DNS records with validation
- **Dynamic DNS**: Complete Dynamic DNS lifecycle management
- **Error Handling**: Detailed error information and exception handling
- **Extensibility**: Custom HTTP client support via interface
- **Type Safety**: Strong typing for all DNS record types and zone types

### Supported Record Types
- A, AAAA, CNAME, MX, NS, SOA, SRV, TXT
- CAA, TLSA, SMIMEA, SSHFP, DS
- HTTPS, SVCB, CERT, URI, RP, LOC, OPENPGPKEY

### Technical Details
- PHP 7.4+ compatibility
- PSR-4 autoloading standard
- Comprehensive unit tests
- Code style compliance (PSR-12)
- Static analysis with PHPStan
- MIT License
