<?php

namespace Tests\Feature\BlackBox;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class A01_AutentikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_pb_01_registrasi_akun(): void
    {
        $response = $this->post('/register', [
            'name' => 'Guru Baru',
            'email' => 'gurubaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'gurubaru@example.com',
            'role' => 'guru'
        ]);
    }

    public function test_pb_02_registrasi_surel_ganda(): void
    {
        User::factory()->create([
            'email' => 'gurubaru@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Guru Lain',
            'email' => 'gurubaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_pb_03_login_kredensial_benar(): void
    {
        $user = User::factory()->create([
            'email' => 'guru_baru@example.com',
            'password' => bcrypt('password123'),
            'role' => 'guru'
        ]);

        $response = $this->post('/login', [
            'email' => 'guru_baru@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_pb_04_login_kredensial_salah(): void
    {
        $user = User::factory()->create([
            'email' => 'guru_baru@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'guru_baru@example.com',
            'password' => 'salahpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}
