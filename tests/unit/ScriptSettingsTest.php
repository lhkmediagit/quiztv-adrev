<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ScriptSettingModel;

/**
 * @internal
 */
final class ScriptSettingsTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $namespace = 'App';

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test case: Verify that ScriptSettingModel can successfully save and retrieve script configs.
     */
    public function testScriptSettingsDatabaseModel(): void
    {
        $model = new ScriptSettingModel();

        // 1. Save test configurations
        $testHeader = '<script>console.log("Antigravity Header Test");</script>';
        $testBody   = '<script>console.log("Antigravity Body Test");</script>';
        $testFooter = '<script>console.log("Antigravity Footer Test");</script>';

        $this->assertTrue($model->setSetting('header_scripts', $testHeader));
        $this->assertTrue($model->setSetting('body_scripts', $testBody));
        $this->assertTrue($model->setSetting('footer_scripts', $testFooter));

        // 2. Fetch configurations individually
        $this->assertEquals($testHeader, $model->getSetting('header_scripts'));
        $this->assertEquals($testBody, $model->getSetting('body_scripts'));
        $this->assertEquals($testFooter, $model->getSetting('footer_scripts'));

        // 3. Fetch all configurations
        $allSettings = $model->getAllSettings();
        $this->assertEquals($testHeader, $allSettings['header_scripts'] ?? null);
        $this->assertEquals($testBody, $allSettings['body_scripts'] ?? null);
        $this->assertEquals($testFooter, $allSettings['footer_scripts'] ?? null);

        // 4. Restore/Clear values
        $model->setSetting('header_scripts', '');
        $model->setSetting('body_scripts', '');
        $model->setSetting('footer_scripts', '');
        $this->assertEquals('', $model->getSetting('header_scripts'));
        $this->assertEquals('', $model->getSetting('body_scripts'));
        $this->assertEquals('', $model->getSetting('footer_scripts'));
    }

    /**
     * Test case: Verify that the get_custom_script helper retrieves script configuration properly.
     */
    public function testScriptSettingsHelper(): void
    {
        $model = new ScriptSettingModel();

        // Save a test snippet
        $snippetHeader = '<meta name="antigravity-custom" content="test-header">';
        $snippetBody   = '<meta name="antigravity-custom" content="test-body">';
        $model->setSetting('header_scripts', $snippetHeader);
        $model->setSetting('body_scripts', $snippetBody);

        // Load the helper
        helper('settings');

        // Retrieve and test value
        $this->assertEquals($snippetHeader, get_custom_script('header'));
        $this->assertEquals($snippetBody, get_custom_script('body'));

        // Reset
        $model->setSetting('header_scripts', '');
        $model->setSetting('body_scripts', '');
    }

    /**
     * Test case: Verify admin script settings routes are registered in Router.
     */
    public function testScriptSettingsRoutesRegistered(): void
    {
        // Call script-settings routes, which should redirect to login as they are protected by adminAuth filter
        $resultGet = $this->call('get', 'admin/script-settings');
        $resultGet->assertRedirectTo(site_url('login'));

        $resultPost = $this->call('post', 'admin/script-settings/update');
        $resultPost->assertRedirectTo(site_url('login'));
    }
}
