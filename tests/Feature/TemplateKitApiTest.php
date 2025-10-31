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

    /**
     * Test extensions search endpoint returns template kits
     */
    public function test_extensions_search_endpoint_returns_template_kits(): void
    {
        TemplateKit::factory()->count(5)->create([
            'category' => 'Template Kits',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/extensions/search?type=wordpress&categories=Template Kits&include_template_kits=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'category', 'is_active'],
                ],
                'meta' => ['current_page', 'total', 'per_page'],
            ]);
    }

    /**
     * Test extensions search with search terms
     */
    public function test_extensions_search_with_search_terms(): void
    {
        TemplateKit::factory()->create([
            'name' => 'Business Template Kit',
            'category' => 'Template Kits',
            'is_active' => true,
        ]);

        TemplateKit::factory()->create([
            'name' => 'Portfolio Template Kit',
            'category' => 'Template Kits',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/extensions/search?type=wordpress&search_terms=Business');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertStringContainsString('Business', $data[0]['name']);
    }

    /**
     * Test extensions search with industries filter
     */
    public function test_extensions_search_with_industries(): void
    {
        TemplateKit::factory()->create([
            'name' => 'Tech Template Kit',
            'category' => 'Template Kits',
            'industries' => ['Technology', 'Software'],
            'is_active' => true,
        ]);

        TemplateKit::factory()->create([
            'name' => 'Health Template Kit',
            'category' => 'Template Kits',
            'industries' => ['Healthcare', 'Medical'],
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/extensions/search?type=wordpress&industries=Technology');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertEquals('Tech Template Kit', $data[0]['name']);
    }

    /**
     * Test extensions search with tags filter
     */
    public function test_extensions_search_with_tags(): void
    {
        TemplateKit::factory()->create([
            'name' => 'Modern Template Kit',
            'category' => 'Template Kits',
            'tags' => ['modern', 'minimal'],
            'is_active' => true,
        ]);

        TemplateKit::factory()->create([
            'name' => 'Classic Template Kit',
            'category' => 'Template Kits',
            'tags' => ['classic', 'traditional'],
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/extensions/search?type=wordpress&tags=modern');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertEquals('Modern Template Kit', $data[0]['name']);
    }

    /**
     * Test extensions search pagination
     */
    public function test_extensions_search_pagination(): void
    {
        TemplateKit::factory()->count(20)->create([
            'category' => 'Template Kits',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/extensions/search?type=wordpress&page=2');

        $response->assertStatus(200)
            ->assertJsonPath('meta.current_page', 2);
    }

    /**
     * Test extensions search only returns active template kits
     */
    public function test_extensions_search_only_returns_active_template_kits(): void
    {
        TemplateKit::factory()->create([
            'name' => 'Active Template',
            'category' => 'Template Kits',
            'is_active' => true,
        ]);

        TemplateKit::factory()->create([
            'name' => 'Inactive Template',
            'category' => 'Template Kits',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/extensions/search?type=wordpress');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals(1, count($data));
        $this->assertEquals('Active Template', $data[0]['name']);
    }
}
