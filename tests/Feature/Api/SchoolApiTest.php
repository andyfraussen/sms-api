<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\ApiTestCase;

abstract class SchoolApiTest extends ApiTestCase
{
    use RefreshDatabase;

    protected function actingAsRole(string $role): self
    {
        $user = User::factory()->create()->assignRole($role);
        return $this->actingAs($user, 'sanctum');
    }
}
