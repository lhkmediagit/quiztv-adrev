<?php

/**
 * Settings Helper
 * Provides utility functions for rendering general settings and script injections.
 * Loaded globally via BaseController.
 */

if (!function_exists('get_custom_script')) {
    /**
     * Get and return the custom script code for a position (header or footer).
     * The results are cached statically for the lifecycle of the request.
     *
     * @param string $position  Either 'header' or 'footer'
     * @return string           The raw custom script code (or empty string)
     */
    function get_custom_script(string $position): string
    {
        static $scripts = null;
        if ($scripts === null) {
            try {
                $model = new \App\Models\ScriptSettingModel();
                $scripts = $model->getAllSettings();
            } catch (\Exception $e) {
                // Return empty if database isn't fully set up or model fails
                return '';
            }
        }

        if ($position === 'body') {
            $key = 'body_scripts';
        } elseif ($position === 'footer') {
            $key = 'footer_scripts';
        } else {
            $key = 'header_scripts';
        }
        return $scripts[$key] ?? '';
    }
}
