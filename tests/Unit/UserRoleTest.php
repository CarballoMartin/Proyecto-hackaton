<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRoleTest extends TestCase
{   

    use RefreshDatabase;
    /** @test */
    public function reconoce_el_rol_correctamente()
    {
        $user = User::factory()->create(['rol' => 'superadmin']);

        $this->assertTrue($user->hasRole('superadmin'));
        $this->assertFalse($user->hasRole('institucional'));
        $this->assertTrue($user->hasRole(['institucional', 'superadmin']));
        $this->assertFalse($user->hasRole(['institucional', 'productor']));
    }
}
