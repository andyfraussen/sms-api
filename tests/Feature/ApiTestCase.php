<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function actingAsRole(string $role): self
    {
        $roleModel = Role::findOrCreate($role, 'web');

        $user = User::factory()->create();
        $user->assignRole($roleModel);

        return $this->actingAs($user, 'sanctum');
    }
}
