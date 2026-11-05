<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Smoke test halaman publik — pastikan refactor tidak mematahkan
 * alur user biasa (tanpa login).
 */
class HomeSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_root_redirect_ke_user_home(): void
    {
        $this->get('/')->assertRedirect(route('user.home'));
        $this->get('/user')->assertOk();
    }

    public function test_halaman_publik_user_semua_200(): void
    {
        $this->get(route('user.about.index'))->assertOk();
        $this->get(route('user.products.index'))->assertOk();
        $this->get(route('user.categories.index'))->assertOk();
        $this->get('/products/search?query=ref')->assertOk();
    }

    public function test_kategori_manapun_200(): void
    {
        $ids = \App\Models\Category::pluck('id');
        $this->assertTrue($ids->isNotEmpty(), 'Seed kategori tidak boleh kosong');

        foreach ($ids as $id) {
            $this->get(route('user.categories.products', $id))->assertOk();
        }
    }

    public function test_detail_product_pertama_200(): void
    {
        $id = \App\Models\Product::value('id');
        if (!$id) {
            $this->markTestSkipped('Belum ada product di seed');
        }
        $this->get(route('user.products.show', $id))->assertOk();
    }

    public function test_login_page_200(): void
    {
        $this->get('/login')->assertOk();
    }
}
