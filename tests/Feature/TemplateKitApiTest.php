<?php

namespace Tests\Feature;

use App\Models\TemplateKit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateKitApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test listing all template kits
     */
    public function test_can_list_template_kits(): void
    {
        TemplateKit::factory()->count(3)->create();

        $response = $this->getJson('/api/template-kits');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'description', 'category', 'author', 'version', 'price', 'is_active'],
                ],
            ]);
    }

    /**
     * Test getting a single template kit
     */
    public function test_can_get_single_template_kit(): void
    {
        $templateKit = TemplateKit::factory()->create();

        $response = $this->getJson("/api/template-kits/{$templateKit->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $templateKit->id,
                    'name' => $templateKit->name,
                ],
            ]);
    }

    /**
     * Test creating a template kit requires authentication
     */
    public function test_creating_template_kit_requires_authentication(): void
    {
        $response = $this->postJson('/api/template-kits', [
            'name' => 'Test Template',
            'price' => 29.99,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can create template kit
     */
    public function test_authenticated_user_can_create_template_kit(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/template-kits', [
                'name' => 'New Template',
                'description' => 'A test template',
                'category' => 'Test',
                'author' => 'Test Author',
                'version' => '1.0.0',
                'price' => 29.99,
                'is_active' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'description', 'category'],
            ]);

        $this->assertDatabaseHas('template_kits', [
            'name' => 'New Template',
            'category' => 'Test',
        ]);
    }

    /**
     * Test authenticated user can update template kit
     */
    public function test_authenticated_user_can_update_template_kit(): void
    {
        $user = User::factory()->create();
        $templateKit = TemplateKit::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/template-kits/{$templateKit->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Template Kit updated successfully',
                'data' => [
                    'name' => 'Updated Name',
                ],
            ]);

        $this->assertDatabaseHas('template_kits', [
            'id' => $templateKit->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test authenticated user can delete template kit
     */
    public function test_authenticated_user_can_delete_template_kit(): void
    {
        $user = User::factory()->create();
        $templateKit = TemplateKit::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/template-kits/{$templateKit->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Template Kit deleted successfully',
            ]);

        $this->assertDatabaseMissing('template_kits', [
            'id' => $templateKit->id,
        ]);
    }

    /**
     * Test filtering template kits by category
     */
    public function test_can_filter_template_kits_by_category(): void
    {
        TemplateKit::factory()->create(['category' => 'Business']);
        TemplateKit::factory()->create(['category' => 'Portfolio']);

        $response = $this->getJson('/api/template-kits?category=Business');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
        $this->assertEquals('Business', $response->json('data.0.category'));
    }

    /**
     * Test rate limiting is configured
     */
    public function test_api_rate_limiting_is_configured(): void
    {
        // Test that rate limiting headers are present
        $response = $this->getJson('/api/template-kits');

        $response->assertStatus(200)
            ->assertHeader('X-RateLimit-Limit')
            ->assertHeader('X-RateLimit-Remaining');
    }
}
