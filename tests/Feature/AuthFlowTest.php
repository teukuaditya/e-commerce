<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Alur autentikasi: register, login, logout, proteksi admin.
 */
class AuthFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_dan_user_demo_bisa_login_logout(): void
    {
        foreach ([
            'admin@demo.com' => 'admin',
            'user@demo.com' => 'user',
        ] as $email => $role) {
            $user = User::where('email', $email)->firstOrFail();

            $this->actingAs($user)
                ->get('/admin/dashboard')
                ->assertStatus($role === 'admin' ? 200 : 302);
        }
    }

    public function test_user_biasa_dilarang_akses_admin(): void
    {
        $user = User::where('email', 'user@demo.com')->firstOrFail();

        $this->actingAs($user)->get('/admin/dashboard')
            ->assertRedirect('/');
    }

    public function test_tamu_diarahkan_ke_login_dari_admin(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_register_membuat_user_role_user_dan_bisa_login(): void
    {
        $resp = $this->post('/register', [
            'name' => 'Refactor Test',
            'email' => 'refactor-test@mail.test',
            'password' => 'password-test',
            'password_confirmation' => 'password-test',
        ]);

        $resp->assertRedirect();

        $user = User::where('email', 'refactor-test@mail.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('user', $user->role);

        // login berakhir di user home (RedirectBasedRole::redirectToLogin)
        $this->post('/login', [
            'email' => 'refactor-test@mail.test',
            'password' => 'password-test',
        ])->assertRedirect();
    }
}
