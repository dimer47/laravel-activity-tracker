<?php

namespace Dimer47\LaravelActivityTracker\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Dimer47\LaravelActivityTracker\App\Http\Controllers\ActivityTrackerController;
use Dimer47\LaravelActivityTracker\App\Models\Activity;
use Dimer47\LaravelActivityTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DateFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityTrackerController $controller;
    protected \ReflectionMethod $applyDateFilterMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new ActivityTrackerController();

        // Get access to private method
        $reflection = new \ReflectionClass($this->controller);
        $this->applyDateFilterMethod = $reflection->getMethod('applyDateFilter');
        $this->applyDateFilterMethod->setAccessible(true);

        Config::set('LaravelActivityTracker.enableDateFiltering', true);
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
    public function it_filters_activities_by_exact_date()
    {
        $specificDate = Carbon::parse('2024-01-15');

        $activity1 = $this->createActivity([
            'created_at' => $specificDate,
            'description' => 'Activity on specific date',
        ]);

        $activity2 = $this->createActivity([
            'created_at' => $specificDate->copy()->addDay(),
            'description' => 'Activity on next day',
        ]);

        $request = new Request([
            'date_from' => '2024-01-15',
            'date_to' => '2024-01-15',
        ]);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($activity1->id, $results->first()->id);
    }

    #[Test]
    public function it_filters_activities_by_date_range()
    {
        $activity1 = $this->createActivity([
            'created_at' => Carbon::parse('2024-01-06'),
            'description' => 'Activity in range',
        ]);

        $activity2 = $this->createActivity([
            'created_at' => Carbon::parse('2024-01-11'),
            'description' => 'Another activity in range',
        ]);

        $activity3 = $this->createActivity([
            'created_at' => Carbon::parse('2024-02-05'),
            'description' => 'Activity outside range',
        ]);

        $request = new Request([
            'date_from' => '2024-01-01',
            'date_to' => '2024-01-31',
        ]);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $activity1->id));
        $this->assertTrue($results->contains('id', $activity2->id));
        $this->assertFalse($results->contains('id', $activity3->id));
    }

    #[Test]
    public function it_filters_activities_by_today_period()
    {
        $today = $this->createActivity([
            'created_at' => now(),
            'description' => 'Today activity',
        ]);

        $yesterday = $this->createActivity([
            'created_at' => now()->subDay(),
            'description' => 'Yesterday activity',
        ]);

        $request = new Request(['period' => 'today']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($today->id, $results->first()->id);
    }

    #[Test]
    public function it_filters_activities_by_yesterday_period()
    {
        $today = $this->createActivity([
            'created_at' => now(),
            'description' => 'Today activity',
        ]);

        $yesterday = $this->createActivity([
            'created_at' => now()->subDay(),
            'description' => 'Yesterday activity',
        ]);

        $request = new Request(['period' => 'yesterday']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($yesterday->id, $results->first()->id);
    }

    #[Test]
    public function it_filters_activities_by_last_7_days_period()
    {
        $today = $this->createActivity(['created_at' => now()]);
        $threeDaysAgo = $this->createActivity(['created_at' => now()->subDays(3)]);
        $tenDaysAgo = $this->createActivity(['created_at' => now()->subDays(10)]);

        $request = new Request(['period' => 'last_7_days']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $today->id));
        $this->assertTrue($results->contains('id', $threeDaysAgo->id));
        $this->assertFalse($results->contains('id', $tenDaysAgo->id));
    }

    #[Test]
    public function it_filters_activities_by_last_30_days_period()
    {
        $recent = $this->createActivity(['created_at' => now()->subDays(15)]);
        $old = $this->createActivity(['created_at' => now()->subDays(45)]);

        $request = new Request(['period' => 'last_30_days']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($recent->id, $results->first()->id);
    }

    #[Test]
    public function it_filters_activities_by_last_3_months_period()
    {
        $recent = $this->createActivity(['created_at' => now()->subMonths(1)]);
        $old = $this->createActivity(['created_at' => now()->subMonths(4)]);

        $request = new Request(['period' => 'last_3_months']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($recent->id, $results->first()->id);
    }

    #[Test]
    public function it_filters_activities_by_last_6_months_period()
    {
        $recent = $this->createActivity(['created_at' => now()->subMonths(3)]);
        $old = $this->createActivity(['created_at' => now()->subMonths(8)]);

        $request = new Request(['period' => 'last_6_months']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($recent->id, $results->first()->id);
    }

    #[Test]
    public function it_filters_activities_by_last_year_period()
    {
        $recent = $this->createActivity(['created_at' => now()->subMonths(6)]);
        $old = $this->createActivity(['created_at' => now()->subYear()->subMonth()]);

        $request = new Request(['period' => 'last_year']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(1, $results);
        $this->assertEquals($recent->id, $results->first()->id);
    }

    #[Test]
    public function it_handles_invalid_period_gracefully()
    {
        $activity = $this->createActivity();

        $request = new Request(['period' => 'invalid_period']);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        // Should return all activities when period is invalid
        $this->assertCount(1, $results);
        $this->assertEquals($activity->id, $results->first()->id);
    }

    #[Test]
    public function it_handles_empty_request_gracefully()
    {
        $activity = $this->createActivity();

        $request = new Request([]);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        // Should return all activities when no filters are applied
        $this->assertCount(1, $results);
        $this->assertEquals($activity->id, $results->first()->id);
    }

    #[Test]
    public function date_range_takes_priority_over_period()
    {
        $activity1 = $this->createActivity(['created_at' => now()->subDays(5)]);
        $activity2 = $this->createActivity(['created_at' => now()->subDays(15)]);
        $activity3 = $this->createActivity(['created_at' => now()->subDays(25)]);

        $request = new Request([
            'date_from' => now()->subDays(10)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'period' => 'last_30_days',
        ]);

        $query = Activity::query();
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        // Date range should take priority
        $this->assertCount(1, $results);
        $this->assertEquals($activity1->id, $results->first()->id);
    }

    #[Test]
    public function it_preserves_query_order()
    {
        $activity1 = $this->createActivity([
            'created_at' => now()->subDays(1),
            'description' => 'First activity',
        ]);

        $activity2 = $this->createActivity([
            'created_at' => now()->subDays(2),
            'description' => 'Second activity',
        ]);

        $request = new Request(['period' => 'last_7_days']);

        $query = Activity::query()->orderBy('created_at', 'desc');
        $filteredQuery = $this->applyDateFilterMethod->invoke($this->controller, $query, $request);
        $results = $filteredQuery->get();

        $this->assertCount(2, $results);
        $this->assertEquals($activity1->id, $results->first()->id);
        $this->assertEquals($activity2->id, $results->last()->id);
    }
}
