<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdSettingModel;

/**
 * Controller: AdSettingsController
 * Manages the Google Ad Manager settings page in the admin panel.
 * Allows admins to configure network codes, ad unit slots, and toggle ad types.
 */
class AdSettingsController extends BaseController
{
    /**
     * Display the ad settings form with current values.
     */
    public function index()
    {
        $model = new AdSettingModel();
        $settings = $model->getAllSettings();

        return view('admin/ad_settings', [
            'settings' => $settings,
            'title'    => 'Ad Manager Settings - QuizTv',
        ]);
    }

    /**
     * Save all ad settings from the form submission.
     * Validates and enforces GAM policy constraints (e.g., minimum refresh interval).
     */
    public function update()
    {
        $model = new AdSettingModel();

        // Define all expected setting keys with their sanitization
        $settingKeys = [
            'ads_enabled'            => 'trim',
            'gam_network_code'       => 'trim',
            'banner_enabled'         => 'trim',
            'banner_home_slot'       => 'trim',
            'banner_quiz_slot'       => 'trim',
            'banner_play_slot'       => 'trim',
            'banner_refresh_seconds' => 'intval',
            'banner_size'            => 'trim',
            'rewarded_enabled'       => 'trim',
            'rewarded_slot'          => 'trim',
            'rewarded_message'       => 'trim',
            'interstitial_enabled'   => 'trim',
            'interstitial_slot'      => 'trim',
        ];

        foreach ($settingKeys as $key => $sanitizer) {
            $value = $this->request->getPost($key) ?? '';

            // Handle checkbox toggles (unchecked = not submitted)
            if (in_array($key, ['ads_enabled', 'banner_enabled', 'rewarded_enabled', 'interstitial_enabled'])) {
                $value = $this->request->getPost($key) ? '1' : '0';
            }

            // Apply sanitization
            if ($sanitizer === 'intval') {
                $value = (string) intval($value);
            } else {
                $value = trim($value);
            }

            // Enforce GAM policy: minimum 30s banner refresh
            if ($key === 'banner_refresh_seconds') {
                $value = (string) max(30, (int) $value);
            }

            // Validate banner size values
            if ($key === 'banner_size') {
                $validSizes = ['responsive', '728x90', '336x280', '300x250', '320x50'];
                if (!in_array($value, $validSizes)) {
                    $value = 'responsive';
                }
            }

            $model->setSetting($key, $value);
        }

        return redirect()->to(site_url('admin/ad-settings'))
                         ->with('success', 'Ad Manager settings saved successfully!');
    }
}
