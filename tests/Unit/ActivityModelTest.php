<?php

namespace Dimer47\LaravelActivityTracker\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Dimer47\LaravelActivityTracker\App\Models\Activity;
use Dimer47\LaravelActivityTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ActivityModelTest extends TestCase
{
    use RefreshDatabase;

    protected function createActivity(array $attributes = []): Activity
    {
        $defaults = [
            'description' => 'Test activity',
            'userType' => 'Guest',
            'route' => 'https://example.com',
            'ipAddress' => '127.0.0.1',
            'methodType' => 'GET',
            'userAgent' => 'Mozilla/5.0',
            'locale' => 'en',
        ];

        $data = array_merge($defaults, $attributes);

        return Activity::create($data);
    }

    #[Test]
    public function it_can_store_relation_fields()
    {
        $activity = $this->createActivity([
            'description' => 'Created an order',
            'relId' => 123,
            'relModel' => 'App\\Models\\Order',
        ]);

        $this->assertDatabaseHas('laravel_activity_tracker', [
            'id' => $activity->id,
            'relId' => 123,
            'relModel' => 'App\\Models\\Order',
        ]);
    }

    #[Test]
    public function it_can_have_nullable_relation_fields()
    {
        $activity = $this->createActivity([
            'description' => 'General activity',
        ]);

        $this->assertDatabaseHas('laravel_activity_tracker', [
            'id' => $activity->id,
        ]);

        $this->assertNull($activity->relId);
        $this->assertNull($activity->relModel);
    }

    #[Test]
    public function it_casts_relation_fields_correctly()
    {
        $activity = $this->createActivity([
            'relId' => '456',
            'relModel' => 'App\\Models\\User',
        ]);

        $this->assertIsInt($activity->relId);
        $this->assertEquals(456, $activity->relId);
        $this->assertIsString($activity->relModel);
    }

    #[Test]
    public function it_includes_relation_fields_in_rules()
    {
        $rules = Activity::rules();

        $this->assertArrayHasKey('relId', $rules);
        $this->assertArrayHasKey('relModel', $rules);
        $this->assertEquals('nullable|integer', $rules['relId']);
        $this->assertEquals('nullable|string', $rules['relModel']);
    }

    #[Test]
    public function it_validates_route_as_url()
    {
        $rules = Activity::rules();

        $this->assertArrayHasKey('route', $rules);
        $this->assertEquals('nullable|url', $rules['route']);
    }

    #[Test]
    public function it_can_merge_custom_rules()
    {
        $customRules = ['custom_field' => 'required|string'];
        $rules = Activity::rules($customRules);

        $this->assertArrayHasKey('custom_field', $rules);
        $this->assertEquals('required|string', $rules['custom_field']);
    }
}
