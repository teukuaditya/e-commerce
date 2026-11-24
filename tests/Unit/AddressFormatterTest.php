<?php

namespace Tests\Unit;

use App\Support\AddressFormatter;
use PHPUnit\Framework\TestCase;

class AddressFormatterTest extends TestCase
{
    public function test_formats_address_correctly(): void
    {
        $data = [
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10',
            'destination_name' => 'Jakarta Barat',
            'destination_id' => '12345',
        ];

        $formatted = AddressFormatter::format($data);
        $this->assertStringContainsString('Nama Penerima: Budi Santoso', $formatted);
        $this->assertStringContainsString('No. Telepon: 081234567890', $formatted);
        $this->assertStringContainsString('Alamat: Jl. Merdeka No. 10', $formatted);
        $this->assertStringContainsString('Destination: Jakarta Barat', $formatted);
        $this->assertStringContainsString('Destination ID: 12345', $formatted);
    }

    public function test_parses_canonical_indonesian_format(): void
    {
        $raw = 'Nama Penerima: Siti Aminah, No. Telepon: 089876543210, Alamat: Jl. Sudirman No. 5, Destination: Bandung, Destination ID: 6789';
        $parsed = AddressFormatter::parse($raw);

        $this->assertSame('Siti Aminah', $parsed['recipient_name']);
        $this->assertSame('089876543210', $parsed['recipient_phone']);
        $this->assertSame('Jl. Sudirman No. 5', $parsed['street_address']);
        $this->assertSame('Bandung', $parsed['destination_name']);
        $this->assertSame('6789', $parsed['destination_id']);
    }

    public function test_parses_legacy_english_format(): void
    {
        $raw = 'Recipient Name: John Doe, Phone: 08111222333, Address: Suite 42, Destination: Surabaya, Destination ID: 4421';
        $parsed = AddressFormatter::parse($raw);

        $this->assertSame('John Doe', $parsed['recipient_name']);
        $this->assertSame('08111222333', $parsed['recipient_phone']);
        $this->assertSame('Suite 42', $parsed['street_address']);
        $this->assertSame('Surabaya', $parsed['destination_name']);
        $this->assertSame('4421', $parsed['destination_id']);
    }

    public function test_parses_multiline_address_and_empty_destination_id(): void
    {
        $raw = "Nama Penerima: Aditya, No. Telepon: 087883623555, Alamat: Jl. Raya No. 10\nRT 01 / RW 02\nKel. Menteng, Destination: Jakarta Pusat, Destination ID: ";
        $parsed = AddressFormatter::parse($raw);

        $this->assertSame('Aditya', $parsed['recipient_name']);
        $this->assertSame('087883623555', $parsed['recipient_phone']);
        $this->assertSame("Jl. Raya No. 10\nRT 01 / RW 02\nKel. Menteng", $parsed['street_address']);
        $this->assertSame('Jakarta Pusat', $parsed['destination_name']);
        $this->assertSame('', $parsed['destination_id']);
        $this->assertSame('Aditya', $parsed['recipientName']);
    }

    public function test_returns_empty_on_null_or_empty_address(): void
    {
        $parsed = AddressFormatter::parse(null);
        $this->assertNull($parsed['recipient_name']);

        $parsedEmpty = AddressFormatter::parse('');
        $this->assertNull($parsedEmpty['recipient_name']);
    }
}
