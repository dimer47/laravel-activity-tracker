<?php

namespace Dimer47\LaravelActivityTracker\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Dimer47\LaravelActivityTracker\App\Http\Controllers\ActivityTrackerController;
use Dimer47\LaravelActivityTracker\App\Models\Activity;
use Dimer47\LaravelActivityTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ActivityTrackerControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityTrackerController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ActivityTrackerController();
    }

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

        // Extract created_at if provided (not mass assignable)
        $createdAt = $data['created_at'] ?? null;
        unset($data['created_at']);

        $activity = Activity::create($data);

        // Set created_at manually if provided
        if ($createdAt) {
            $activity->created_at = $createdAt;
            $activity->save();
        }

        return $activity;
    }

    #[Test]
    public function it_can_create_activity_model()
    {
        $activity = $this->createActivity([
            'description' => 'Test activity',
            'contentType' => 'text/html',
            'contentId' => '1',
            'route' => 'https://example.com/test',
            'ipAddress' => '192.168.1.1',
        ]);

        $this->assertDatabaseHas('laravel_activity_tracker', [
            'description' => 'Test activity',
        ]);
    }

    #[Test]
    public function it_can_filter_activities_by_date_range()
    {
        // Create test activities with different dates
        $today = $this->createActivity([
            'description' => 'Today activity',
            'created_at' => now(),
        ]);

        $yesterday = $this->createActivity([
            'description' => 'Yesterday activity',
            'created_at' => now()->subDay(),
        ]);

        $lastWeek = $this->createActivity([
            'description' => 'Last week activity',
            'created_at' => now()->subWeek(),
        ]);

        // Test filtering by date range using reflection to access private method
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('applyDateFilter');
        $method->setAccessible(true);

        $request = new Request([
            'date_from' => now()->subDay()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]);

        $query = Activity::orderBy('created_at', 'desc');
        $filteredQuery = $method->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $today->id));
        $this->assertTrue($results->contains('id', $yesterday->id));
        $this->assertFalse($results->contains('id', $lastWeek->id));
    }

    #[Test]
    public function it_can_filter_activities_by_today_period()
    {
        $today = $this->createActivity([
            'description' => 'Today activity',
            'created_at' => now(),
        ]);

        $yesterday = $this->createActivity([
            'description' => 'Yesterday activity',
            'created_at' => now()->subDay(),
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('applyDateFilter');
        $method->setAccessible(true);

        $request = new Request(['period' => 'today']);
        $query = Activity::orderBy('created_at', 'desc');
        $filteredQuery = $method->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains('id', $today->id));
    }

    #[Test]
    public function it_can_filter_activities_by_last_7_days()
    {
        $today = $this->createActivity([
            'description' => 'Today activity',
            'created_at' => now(),
        ]);

        $lastMonth = $this->createActivity([
            'description' => 'Last month activity',
            'created_at' => now()->subMonth(),
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('applyDateFilter');
        $method->setAccessible(true);

        $request = new Request(['period' => 'last_7_days']);
        $query = Activity::orderBy('created_at', 'desc');
        $filteredQuery = $method->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains('id', $today->id));
        $this->assertFalse($results->contains('id', $lastMonth->id));
    }

    #[Test]
    public function it_can_filter_activities_by_last_30_days()
    {
        $recent = $this->createActivity([
            'description' => 'Recent activity',
            'created_at' => now()->subDays(15),
        ]);

        $old = $this->createActivity([
            'description' => 'Old activity',
            'created_at' => now()->subDays(45),
        ]);

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('applyDateFilter');
        $method->setAccessible(true);

        $request = new Request(['period' => 'last_30_days']);
        $query = Activity::orderBy('created_at', 'desc');
        $filteredQuery = $method->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains('id', $recent->id));
        $this->assertFalse($results->contains('id', $old->id));
    }

    #[Test]
    public function activity_model_uses_soft_deletes()
    {
        $activity = $this->createActivity([
            'description' => 'Test activity',
        ]);

        $activity->delete();

        $this->assertSoftDeleted('laravel_activity_tracker', [
            'id' => $activity->id,
        ]);

        // Can still find with trashed
        $this->assertNotNull(Activity::withTrashed()->find($activity->id));
    }

    #[Test]
    public function it_ignores_date_filtering_when_disabled()
    {
        Config::set('LaravelActivityTracker.enableDateFiltering', false);

        $activity = $this->createActivity([
            'description' => 'Old activity',
            'created_at' => now()->subYear(),
        ]);

        // Test via exportActivityLog which respects the enableDateFiltering config
        $request = new Request(['format' => 'json', 'period' => 'today']);
        $response = $this->controller->exportActivityLog($request);

        $content = $response->getContent();
        $data = json_decode($content, true);

        // Should return all activities when filtering is disabled
        $this->assertCount(1, $data);
        $this->assertEquals($activity->id, $data[0]['id']);
    }
}
