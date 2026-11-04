<?php

namespace App\Support;

/**
 * Satu sumber kebenaran untuk format & parsing alamat user.
 *
 * Latar belakang (temuan audit T1):
 * sebelumnya CheckoutController memakai format "Recipient Name: ..." (inggris)
 * sedangkan User\UserController memakai format "Nama Penerima: ..." (indonesia),
 * sehingga alamat yang disimpan lewat halaman alamat TIDAK bisa di-parse
 * oleh halaman checkout. Kelas ini menyatukan:
 *   - output  : selalu satu format kanonikal (indonesia, tanda pemisah koma)
 *   - input   : mampu mem-parse KEDUA format lama agar data lama tetap terbaca
 */
class AddressFormatter
{
    /**
     * Label kanonikal ( dipakai saat menyimpan ke database ).
     */
    public const LABEL_NAME = 'Nama Penerima';
    public const LABEL_PHONE = 'No. Telepon';
    public const LABEL_ADDRESS = 'Alamat';
    public const LABEL_DESTINATION = 'Destination';
    public const LABEL_DESTINATION_ID = 'Destination ID';

    /**
     * Membentuk string alamat terstruktur untuk disimpan ke kolom users.address.
     */
    public static function format(array $d): string
    {
        return sprintf(
            '%s: %s, %s: %s, %s: %s, %s: %s, %s: %s',
            self::LABEL_NAME,
            $d['recipient_name'] ?? '',
            self::LABEL_PHONE,
            $d['recipient_phone'] ?? '',
            self::LABEL_ADDRESS,
            $d['address'] ?? '',
            self::LABEL_DESTINATION,
            $d['destination_name'] ?? '',
            self::LABEL_DESTINATION_ID,
            $d['destination_id'] ?? ''
        );
    }

    /**
     * Mem-parse string alamat — memahami format kanonikal ATAU format
     * lama berbahasa inggris ("Recipient Name: ..., Phone: ...").
     *
     * @return array{recipient_name:?string, recipient_phone:?string, street_address:?string, destination_name:?string, destination_id:?string}
     */
    public static function parse(?string $address): array
    {
        $empty = [
            'recipient_name' => null,
            'recipient_phone' => null,
            'street_address' => null,
            'destination_name' => null,
            'destination_id' => null,
        ];

        if (! $address) {
            return $empty;
        }

        // Format kanonikal (indonesia)
        if (preg_match(
            '/' . self::LABEL_NAME . ': (.*?), ' . self::LABEL_PHONE . ': (.*?), ' . self::LABEL_ADDRESS . ': (.*?), ' . self::LABEL_DESTINATION . ': (.*?), ' . self::LABEL_DESTINATION_ID . ': (.*)$/',
            $address,
            $m
        )) {
            return [
                'recipient_name' => trim($m[1] ?? ''),
                'recipient_phone' => trim($m[2] ?? ''),
                'street_address' => trim($m[3] ?? ''),
                'destination_name' => trim($m[4] ?? ''),
                'destination_id' => trim($m[5] ?? ''),
            ];
        }

        // Format legacy berbahasa inggris (dipakai CheckoutController lama)
        if (preg_match(
            '/Recipient Name: (.*?), Phone: (.*?), Address: (.*?), Destination: (.*?), Destination ID: (.*?)(,|$)/',
            $address,
            $m
        )) {
            return [
                'recipient_name' => trim($m[1] ?? ''),
                'recipient_phone' => trim($m[2] ?? ''),
                'street_address' => trim($m[3] ?? ''),
                'destination_name' => trim($m[4] ?? ''),
                'destination_id' => trim($m[5] ?? ''),
            ];
        }

        return $empty;
    }
}
