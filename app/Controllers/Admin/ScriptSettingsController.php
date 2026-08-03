<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ScriptSettingModel;

/**
 * Controller: ScriptSettingsController
 * Manages the custom script (header/footer) settings page in the admin panel.
 */
class ScriptSettingsController extends BaseController
{
    /**
     * Display the script settings form with current values.
     */
    public function index()
    {
        $model = new ScriptSettingModel();
        $settings = $model->getAllSettings();

        return view('admin/script_settings', [
            'settings' => $settings,
            'title'    => 'Custom Script Settings - QuizTv',
        ]);
    }

    /**
     * Save custom header, body, and footer script settings.
     */
    public function update()
    {
        $model = new ScriptSettingModel();

        // Retrieve inputs (optionally base64 prefixed from client-side script settings form)
        $headerScriptsRaw = $this->request->getPost('header_scripts') ?? '';
        $bodyScriptsRaw   = $this->request->getPost('body_scripts') ?? '';
        $footerScriptsRaw = $this->request->getPost('footer_scripts') ?? '';

        // Decode Base64 safely if prepended with 'base64:'
        $headerScripts = (str_starts_with($headerScriptsRaw, 'base64:')) ? base64_decode(substr($headerScriptsRaw, 7)) : $headerScriptsRaw;
        $bodyScripts   = (str_starts_with($bodyScriptsRaw, 'base64:')) ? base64_decode(substr($bodyScriptsRaw, 7)) : $bodyScriptsRaw;
        $footerScripts = (str_starts_with($footerScriptsRaw, 'base64:')) ? base64_decode(substr($footerScriptsRaw, 7)) : $footerScriptsRaw;

        // Save settings to database
        $model->setSetting('header_scripts', $headerScripts);
        $model->setSetting('body_scripts', $bodyScripts);
        $model->setSetting('footer_scripts', $footerScripts);

        return redirect()->to(site_url('admin/script-settings'))
                         ->with('success', 'Custom scripts saved successfully!');
    }
}
