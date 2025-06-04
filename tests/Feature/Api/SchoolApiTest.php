<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\ApiTestCase;

abstract class SchoolApiTest extends ApiTestCase
{
    use RefreshDatabase;
}
