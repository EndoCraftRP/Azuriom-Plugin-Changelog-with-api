<?php

namespace Azuriom\Plugin\Changelog\Tests\Feature;

use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\Changelog\Models\Update;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_updates()
    {
        $response = $this->getJson('/api/changelog/updates');
        $response->assertStatus(200);
    }

    public function test_cannot_create_update_without_token()
    {
        $response = $this->postJson('/api/changelog/updates', [
            'category_id' => 1,
            'name' => 'v1.2.3',
            'description' => 'Test',
        ]);
        $response->assertStatus(401);
    }
}
