<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddressUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_view_address_page(): void
    {
        $user = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/user/address/edit');
        $response->assertStatus(200);
        $response->assertSee('Address');
    }

    public function test_user_can_update_address_via_form(): void
    {
        $user = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post('/user/address/update', [
            'recipient_name' => 'Budi Pengujian',
            'recipient_phone' => '081234567890',
            'address' => "Jalan Pengujian No. 123\nRT 05 / RW 01",
            'destination_name' => 'Jakarta Selatan',
            'destination_id' => '1234',
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertStringContainsString('Budi Pengujian', $user->address);
        $this->assertStringContainsString('081234567890', $user->address);
        $this->assertStringContainsString('Jakarta Selatan', $user->address);
        $this->assertStringContainsString('1234', $user->address);
    }

    public function test_user_can_update_address_via_ajax(): void
    {
        $user = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->postJson('/user/address/update', [
            'recipient_name' => 'Siti Pengujian',
            'recipient_phone' => '089876543210',
            'address' => 'Jalan Melati No. 8',
            'destination_name' => 'Bandung',
            'destination_id' => '5678',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $user->refresh();
        $this->assertStringContainsString('Siti Pengujian', $user->address);
        $this->assertStringContainsString('5678', $user->address);
    }

    public function test_user_can_save_address_without_destination_id(): void
    {
        $user = User::where('role', 'user')->first() ?? User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->postJson('/user/address/save', [
            'recipient_name' => 'Ahmad Tanpa Dest ID',
            'recipient_phone' => '0855555555',
            'address' => 'Jalan Desa No. 1',
            'destination_name' => 'Purwokerto',
            'destination_id' => '',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $user->refresh();
        $this->assertStringContainsString('Ahmad Tanpa Dest ID', $user->address);
    }

    public function test_search_destinations_returns_empty_array_gracefully_when_no_api_key(): void
    {
        $response = $this->getJson('/api/search-destinations?term=jakarta');
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }
}
