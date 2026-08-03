<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model: ScriptSettingModel
 * Manages the 'script_settings' key-value table for custom header and footer script injections.
 */
class ScriptSettingModel extends Model
{
    protected $table            = 'script_settings';
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
}
