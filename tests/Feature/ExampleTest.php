<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_redirects_to_user_home(): void
    {
        // Root '/' adalah redirect ke halaman user (perilaktu nyata applix)
        $this->get('/')->assertRedirect(route('user.home'));
    }
}
