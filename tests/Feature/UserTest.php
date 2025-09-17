<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_admin()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $this->assertTrue($user->isAdmin());
    }
}
