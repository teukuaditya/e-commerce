<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $defaults = $this->defaults();
        $settings = [];
        foreach ($defaults as $key => $meta) {
            $settings[$key] = Setting::getValue($key, $meta['default']);
        }

        return view('pages.admin.settings.index', [
            'settings' => $settings,
            'defaults' => $defaults,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $defaults = $this->defaults();
        $rules = [];
        foreach ($defaults as $key => $meta) {
            if (($meta['type'] ?? '') === 'file') {
                $rules[$key] = $meta['rules'];
            } else {
                $rules[$key] = $meta['rules'];
            }
        }

        $validated = $request->validate($rules);

        foreach ($defaults as $key => $meta) {
            $type = $meta['type'] ?? 'text';
            if ($type === 'file') {
                if ($request->hasFile($key)) {
                    $old = Setting::getValue($key);
                    if ($old && Storage::disk('public')->exists($old)) {
                        Storage::disk('public')->delete($old);
                    }
                    $path = $request->file($key)->store('settings', 'public');
                    Setting::setValue($key, $path, $meta['group'], 'file');
                }
                continue;
            }

            $value = $validated[$key] ?? $meta['default'];
            if ($type === 'number') {
                $value = (string) max(0, (int) $value);
            }
            if ($type === 'boolean') {
                $value = $request->boolean($key) ? '1' : '0';
            }
            Setting::setValue($key, is_string($value) ? trim($value) : (string) $value, $meta['group'], $type);
        }

        Setting::flushCache();

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }

    private function defaults(): array
    {
        return [
            // General
            'store_name' => ['default' => 'DRVN', 'group' => 'general', 'type' => 'text', 'rules' => 'required|string|max:100', 'label' => 'Nama Toko', 'desc' => 'Nama toko yang ditampilkan.'],
            'store_tagline' => ['default' => 'all about essentials', 'group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:150', 'label' => 'Tagline', 'desc' => 'Tagline singkat toko.'],
            'store_email' => ['default' => 'driveventure719@gmail.com', 'group' => 'general', 'type' => 'text', 'rules' => 'nullable|email|max:150', 'label' => 'Email Toko', 'desc' => 'Email kontak utama (tampil di About & footer).'],
            'store_phone' => ['default' => '', 'group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:30', 'label' => 'Telepon', 'desc' => 'Nomor telepon toko.'],
            'store_whatsapp' => ['default' => '', 'group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:30', 'label' => 'WhatsApp CS', 'desc' => 'Nomor WhatsApp customer service (format 62...).'],
            'store_address' => ['default' => 'Jl. Farida, Purwokerto, Jawa Tengah, Indonesia', 'group' => 'general', 'type' => 'textarea', 'rules' => 'nullable|string|max:500', 'label' => 'Alamat Toko', 'desc' => 'Alamat toko (tampil di About & footer).'],
            'operating_hours' => ['default' => 'Senin - Sabtu, 09:00 - 17:00', 'group' => 'general', 'type' => 'text', 'rules' => 'nullable|string|max:150', 'label' => 'Jam Operasional', 'desc' => 'Jam operasional toko.'],
            'about_text' => ['default' => '', 'group' => 'general', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000', 'label' => 'Teks About Tambahan', 'desc' => 'Teks tambahan untuk halaman About (opsional).'],
            'footer_text' => ['default' => 'Pakaian esensial yang bertahan. Produksi lokal, bahan pilihan, harga jujur — semuanya untuk lemari harian Anda.', 'group' => 'general', 'type' => 'textarea', 'rules' => 'nullable|string|max:500', 'label' => 'Teks Footer', 'desc' => 'Deskripsi singkat di footer.'],

            // Social
            'social_instagram' => ['default' => '', 'group' => 'social', 'type' => 'text', 'rules' => 'nullable|string|max:255', 'label' => 'Instagram URL', 'desc' => 'Link Instagram toko.'],
            'social_facebook' => ['default' => '', 'group' => 'social', 'type' => 'text', 'rules' => 'nullable|string|max:255', 'label' => 'Facebook URL', 'desc' => 'Link Facebook toko.'],
            'social_tiktok' => ['default' => '', 'group' => 'social', 'type' => 'text', 'rules' => 'nullable|string|max:255', 'label' => 'TikTok URL', 'desc' => 'Link TikTok toko.'],

            // Commerce
            'currency_symbol' => ['default' => 'Rp', 'group' => 'commerce', 'type' => 'text', 'rules' => 'required|string|max:10', 'label' => 'Simbol Mata Uang', 'desc' => 'Simbol mata uang yang ditampilkan.'],
            'monthly_revenue_goal' => ['default' => '50000000', 'group' => 'commerce', 'type' => 'number', 'rules' => 'required|integer|min:0', 'label' => 'Target Revenue Bulanan', 'desc' => 'Target pendapatan bulanan untuk dashboard admin.'],
            'min_order_amount' => ['default' => '0', 'group' => 'commerce', 'type' => 'number', 'rules' => 'required|integer|min:0', 'label' => 'Minimum Order (Rp)', 'desc' => 'Minimum total belanja. 0 = tidak ada batas.'],
            'default_shipping_fallback' => ['default' => '0', 'group' => 'commerce', 'type' => 'number', 'rules' => 'required|integer|min:0', 'label' => 'Fallback Ongkir (Rp)', 'desc' => 'Ongkir fallback jika tidak ada quote (0 = nonaktif).'],
            'order_id_prefix' => ['default' => 'DRV', 'group' => 'commerce', 'type' => 'text', 'rules' => 'nullable|string|max:20', 'label' => 'Prefix Order ID', 'desc' => 'Prefix untuk ID pesanan baru.'],
            'show_out_of_stock' => ['default' => '1', 'group' => 'commerce', 'type' => 'boolean', 'rules' => 'nullable', 'label' => 'Tampilkan Produk Habis Stok', 'desc' => 'Tampilkan produk yang stoknya habis di katalog.'],

            // Branding files
            'store_logo' => ['default' => '', 'group' => 'branding', 'type' => 'file', 'rules' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048', 'label' => 'Logo Toko', 'desc' => 'Logo toko (PNG/JPG/WEBP/SVG, maks 2MB).'],
            'store_favicon' => ['default' => '', 'group' => 'branding', 'type' => 'file', 'rules' => 'nullable|mimes:png,ico,jpg,jpeg,webp|max:1024', 'label' => 'Favicon', 'desc' => 'Favicon browser (PNG/ICO, maks 1MB).'],
        ];
    }
}
