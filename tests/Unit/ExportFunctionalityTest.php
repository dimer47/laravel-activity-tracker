<?php

namespace Dimer47\LaravelActivityTracker\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Dimer47\LaravelActivityTracker\App\Http\Controllers\ActivityTrackerController;
use Dimer47\LaravelActivityTracker\App\Models\Activity;
use Dimer47\LaravelActivityTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ExportFunctionalityTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityTrackerController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new ActivityTrackerController();

        Config::set('LaravelActivityTracker.enableExport', true);
        Config::set('LaravelActivityTracker.enableDateFiltering', true);
        Config::set('LaravelActivityTracker.enableSearch', true);
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
    public function it_exports_activities_to_csv_format()
    {
        $this->createActivity([
            'description' => 'Test activity',
            'route' => 'https://example.com/test',
            'ipAddress' => '192.168.1.1',
        ]);

        $request = new Request(['format' => 'csv']);
        $response = $this->controller->exportActivityLog($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function it_exports_activities_to_json_format()
    {
        $activity = $this->createActivity([
            'description' => 'Test activity',
            'route' => 'https://example.com/test',
            'ipAddress' => '192.168.1.1',
        ]);

        $request = new Request(['format' => 'json']);
        $response = $this->controller->exportActivityLog($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.json', $response->headers->get('Content-Disposition'));

        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertEquals($activity->id, $data[0]['id']);
    }

    #[Test]
    public function it_exports_activities_to_excel_format()
    {
        $this->createActivity([
            'description' => 'Test activity',
        ]);

        $request = new Request(['format' => 'excel']);
        $response = $this->controller->exportActivityLog($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
    }

    #[Test]
    public function it_returns_error_for_invalid_export_format()
    {
        $this->createActivity();

        $request = new Request(['format' => 'invalid']);
        $response = $this->controller->exportActivityLog($request);

        $this->assertEquals(302, $response->getStatusCode());
    }

    #[Test]
    public function it_uses_csv_as_default_format()
    {
        $this->createActivity();

        $request = new Request([]); // No format specified
        $response = $this->controller->exportActivityLog($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function it_exports_multiple_activities()
    {
        $activity1 = $this->createActivity(['description' => 'First activity']);
        $activity2 = $this->createActivity(['description' => 'Second activity']);

        $request = new Request(['format' => 'json']);
        $response = $this->controller->exportActivityLog($request);

        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertCount(2, $data);
        $this->assertTrue(collect($data)->contains('id', $activity1->id));
        $this->assertTrue(collect($data)->contains('id', $activity2->id));
    }

    #[Test]
    public function it_handles_activities_without_user()
    {
        $activity = $this->createActivity(['userId' => null]);

        $request = new Request(['format' => 'json']);
        $response = $this->controller->exportActivityLog($request);

        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertArrayHasKey('user_email', $data[0]);
        $this->assertNull($data[0]['user_email']);
    }

    #[Test]
    public function it_generates_unique_filenames()
    {
        $this->createActivity();

        $request1 = new Request(['format' => 'csv']);
        $response1 = $this->controller->exportActivityLog($request1);

        sleep(1);

        $request2 = new Request(['format' => 'csv']);
        $response2 = $this->controller->exportActivityLog($request2);

        $filename1 = $response1->headers->get('Content-Disposition');
        $filename2 = $response2->headers->get('Content-Disposition');

        $this->assertNotEquals($filename1, $filename2);
    }

    #[Test]
    public function it_exports_filtered_activities_by_date()
    {
        $today = $this->createActivity([
            'created_at' => now(),
            'description' => 'Today activity',
        ]);

        $yesterday = $this->createActivity([
            'created_at' => now()->subDay(),
            'description' => 'Yesterday activity',
        ]);

        $request = new Request([
            'format' => 'json',
            'period' => 'today',
        ]);

        $response = $this->controller->exportActivityLog($request);
        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertCount(1, $data);
        $this->assertEquals($today->id, $data[0]['id']);
    }

    #[Test]
    public function it_handles_empty_activities_gracefully()
    {
        $request = new Request(['format' => 'json']);
        $response = $this->controller->exportActivityLog($request);

        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    #[Test]
    public function it_preserves_activity_order_in_export()
    {
        $activity1 = $this->createActivity([
            'created_at' => now()->subDays(2),
            'description' => 'First activity',
        ]);

        $activity2 = $this->createActivity([
            'created_at' => now()->subDays(1),
            'description' => 'Second activity',
        ]);

        $request = new Request(['format' => 'json']);
        $response = $this->controller->exportActivityLog($request);

        $content = $response->getContent();
        $data = json_decode($content, true);

        $this->assertCount(2, $data);
        // Should be ordered by created_at desc (newest first)
        $this->assertEquals($activity2->id, $data[0]['id']);
        $this->assertEquals($activity1->id, $data[1]['id']);
    }

    #[Test]
    public function it_includes_required_fields_in_csv()
    {
        $this->createActivity();

        $request = new Request(['format' => 'csv']);
        $response = $this->controller->exportActivityLog($request);

        // Capture streamed response content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $expectedHeaders = ['ID', 'Description', 'Route', 'IP'];

        foreach ($expectedHeaders as $header) {
            $this->assertStringContainsString($header, $content);
        }
    }

    #[Test]
    public function it_includes_required_fields_in_json()
    {
        $this->createActivity();

        $request = new Request(['format' => 'json']);
        $response = $this->controller->exportActivityLog($request);

        $content = $response->getContent();
        $data = json_decode($content, true);

        $expectedFields = ['id', 'description', 'route', 'ip_address', 'created_at'];

        foreach ($expectedFields as $field) {
            $this->assertArrayHasKey($field, $data[0]);
        }
    }
}
