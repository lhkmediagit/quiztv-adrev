<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\UserModel;
use App\Models\AdSettingModel;

/**
 * @internal
 */
final class SecurityAndDatabaseTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test case: Verify password hashes are securely encrypted (not plaintext)
     * and authenticate correctly via password_verify.
     */
    public function testPasswordEncryption(): void
    {
        $rawPassword = 'SecurePassword@123';
        $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

        // Verify it is a valid BCrypt hash
        $info = password_get_info($hashedPassword);
        $this->assertEquals('bcrypt', $info['algoName'] ?? '');

        // Verify it is not plaintext
        $this->assertNotEquals($rawPassword, $hashedPassword);

        // Verify match verification behaves correctly
        $this->assertTrue(password_verify($rawPassword, $hashedPassword));
        $this->assertFalse(password_verify('WrongPassword', $hashedPassword));
    }

    /**
     * Test case: Verify that unauthorized users trying to access dashboard URL without active sessions
     * are redirected to /login route.
     */
    public function testAuthFiltersRedirect(): void
    {
        // Request to user dashboard route without logged-in session data
        $result = $this->call('get', 'user/dashboard');
        $result->assertRedirectTo(base_url('login'));

        // Request to admin panel route without logged-in admin session data
        $resultAdmin = $this->call('get', 'admin');
        $resultAdmin->assertRedirectTo(base_url('login'));
    }

    /**
     * Test case: Verify Cache-Control headers exist on auth filters to prevent back button dashboard cache loads.
     */
    public function testCacheControlHeaders(): void
    {
        // Mock standard auth filter run
        $request = service('request');
        $response = service('response');
        $filter = new \App\Filters\AuthFilter();

        // Run the after callback to set headers
        $filter->after($request, $response);

        $this->assertTrue($response->hasHeader('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->getHeaderLine('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->getHeaderLine('Cache-Control'));

        $this->assertTrue($response->hasHeader('Pragma'));
        $this->assertEquals('no-cache', $response->getHeaderLine('Pragma'));
    }

    /**
     * Test case: Verify Google Ad Manager settings structure has interstitial slots mappings defined.
     */
    public function testAdSettingsStructure(): void
    {
        $model = new AdSettingModel();
        $config = $model->getAdConfig();

        // Check if configuration maps array blocks for banners, rewarded, and interstitials
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('banner', $config);
        $this->assertArrayHasKey('rewarded', $config);
        $this->assertArrayHasKey('interstitial', $config);

        $this->assertArrayHasKey('enabled', $config['interstitial']);
        $this->assertArrayHasKey('slot', $config['interstitial']);
    }
}
