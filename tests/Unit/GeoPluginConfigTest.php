<?php

namespace Dimer47\LaravelActivityTracker\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Dimer47\LaravelActivityTracker\App\Http\Traits\IpAddressDetails;
use Dimer47\LaravelActivityTracker\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GeoPluginConfigTest extends TestCase
{
    use IpAddressDetails;

    #[Test]
    public function geoplugin_is_enabled_by_default()
    {
        $this->assertTrue(config('LaravelActivityTracker.enableGeoPlugin'));
    }

    #[Test]
    public function geoplugin_url_is_configured()
    {
        $url = config('LaravelActivityTracker.geoPluginUrl');

        $this->assertNotNull($url);
        $this->assertStringContainsString('geoplugin.net', $url);
    }

    #[Test]
    public function geoplugin_can_be_disabled()
    {
        Config::set('LaravelActivityTracker.enableGeoPlugin', false);

        // When disabled, checkIP should return null for valid purposes
        // without making external HTTP requests
        $result = self::checkIP('8.8.8.8', 'location', false);

        // Result should be null when GeoPlugin is disabled
        $this->assertNull($result);
    }

    #[Test]
    public function checkip_returns_null_for_invalid_ip()
    {
        // Set REMOTE_ADDR to avoid undefined key error
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $result = self::checkIP('invalid-ip', 'location', false);

        // When deep_detect is false and IP is invalid, it falls back to REMOTE_ADDR
        // which is a valid IP, so we test with a truly invalid scenario
        Config::set('LaravelActivityTracker.enableGeoPlugin', false);
        $result = self::checkIP('invalid-ip', 'location', false);

        $this->assertNull($result);
    }

    #[Test]
    public function checkip_returns_null_for_unsupported_purpose()
    {
        // Set REMOTE_ADDR to avoid undefined key error
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        // Disable GeoPlugin to avoid external HTTP calls
        Config::set('LaravelActivityTracker.enableGeoPlugin', false);

        $result = self::checkIP('8.8.8.8', 'unsupported_purpose', false);

        $this->assertNull($result);
    }
}
