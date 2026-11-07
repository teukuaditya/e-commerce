<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductAjaxPaginationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_product_page_renders_with_grid_classes(): void
    {
        $response = $this->get(route('user.products.index'));
        $response->assertOk();
        $response->assertSee('col-lg-3');
        $response->assertSee('product-grid-item');
    }

    public function test_product_ajax_request_returns_json_and_pagination(): void
    {
        $response = $this->get(route('user.products.index'), ['X-Requested-With' => 'XMLHttpRequest']);
        $response->assertOk();
        $response->assertJsonStructure([
            'count',
            'query',
            'html',
            'pagination',
        ]);
    }

    public function test_product_ajax_search_filters_in_place(): void
    {
        $response = $this->get(route('user.products.index', ['query' => 'Kaos']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $response->assertOk();
        $data = $response->json();
        $this->assertIsInt($data['count']);
        $this->assertEquals('Kaos', $data['query']);
    }

    public function test_search_endpoint_ajax_supports_pagination(): void
    {
        $response = $this->get('/products/search?query=Kaos', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'count',
            'query',
            'html',
            'pagination',
        ]);
    }
}
