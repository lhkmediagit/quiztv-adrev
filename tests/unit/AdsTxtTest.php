<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\AdSettingModel;

/**
 * @internal
 */
final class AdsTxtTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $namespace = 'App';

    /**
     * Test case: Verify ads.txt serving when enabled.
     */
    public function testAdsTxtServingEnabled(): void
    {
        $model = new AdSettingModel();

        // 1. Enable ads.txt and set content
        $testContent = "google.com, pub-1111222233334444, DIRECT, f08c47fec0942fa0\nexample.com, pub-9999999999999999, RESELLER";
        $model->setSetting('ads_txt_enabled', '1');
        $model->setSetting('ads_txt_content', $testContent);

        // 2. Fetch via route
        $result = $this->get('ads.txt');

        // 3. Verify status code is 200, content type is text/plain, and body is correct
        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertEquals($testContent, $result->response()->getBody());
    }

    /**
     * Test case: Verify ads.txt returning 404 when disabled.
     */
    public function testAdsTxtServingDisabled(): void
    {
        $model = new AdSettingModel();

        // 1. Disable ads.txt
        $model->setSetting('ads_txt_enabled', '0');

        // 2. Expect PageNotFoundException
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);

        // 3. Fetch via route
        $this->get('ads.txt');
    }
}
