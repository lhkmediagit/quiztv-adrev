<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: AdSettingModel
 * Manages the 'ad_settings' key-value table for Google Ad Manager configuration.
 * Provides helper methods to get/set individual settings and build config arrays.
 */
class AdSettingModel extends Model
{
    protected $table            = 'ad_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'description',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get a single setting value by key.
     *
     * @param string $key     The setting key
     * @param mixed  $default Default value if key not found
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $row = $this->where('setting_key', $key)->first();
        return $row ? $row->setting_value : $default;
    }

    /**
     * Set (upsert) a single setting value.
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function setSetting(string $key, string $value): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return $this->update($existing->id, ['setting_value' => $value]);
        }
        return (bool) $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
        ]);
    }

    /**
     * Get all settings as an associative array [key => value].
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        $rows = $this->findAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row->setting_key] = $row->setting_value;
        }
        return $settings;
    }

    /**
     * Build a structured ad configuration array for use in views and JS.
     * Returns a safe, ready-to-json_encode structure.
     *
     * @return array
     */
    public function getAdConfig(): array
    {
        $s = $this->getAllSettings();

        $adsEnabled = ($s['ads_enabled'] ?? '0') === '1';
        $networkCode = $s['gam_network_code'] ?? '';

        // Enforce minimum 30s refresh per GAM policy
        $refreshSeconds = max(30, (int)($s['banner_refresh_seconds'] ?? 60));

        return [
            'enabled'          => $adsEnabled && !empty($networkCode),
            'network_code'     => $networkCode,
            'banner' => [
                'enabled'       => $adsEnabled && ($s['banner_enabled'] ?? '0') === '1',
                'home_slot'     => $s['banner_home_slot'] ?? '',
                'quiz_slot'     => $s['banner_quiz_slot'] ?? '',
                'play_slot'     => $s['banner_play_slot'] ?? '',
                'refresh'       => $refreshSeconds,
                'size'          => $s['banner_size'] ?? 'responsive',
            ],
            'rewarded' => [
                'enabled'  => $adsEnabled && ($s['rewarded_enabled'] ?? '0') === '1',
                'slot'     => $s['rewarded_slot'] ?? '',
                'message'  => $s['rewarded_message'] ?? 'Thanks for watching!',
            ],
            'interstitial' => [
                'enabled'  => $adsEnabled && ($s['interstitial_enabled'] ?? '0') === '1',
                'slot'     => $s['interstitial_slot'] ?? '',
            ],
        ];
    }
}
