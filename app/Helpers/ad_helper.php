<?php

/**
 * Ad Helper
 * Provides utility functions for rendering Google Ad Manager ads
 * across all QuizTv views. Loaded globally via BaseController.
 */

if (!function_exists('get_ad_config')) {
    /**
     * Get the ad configuration array from database.
     * Results are cached in a static variable for the request lifecycle.
     *
     * @return array
     */
    function get_ad_config(): array
    {
        static $config = null;
        if ($config === null) {
            $model = new \App\Models\AdSettingModel();
            $config = $model->getAdConfig();
        }
        return $config;
    }
}

if (!function_exists('is_ads_enabled')) {
    /**
     * Quick check if ads are globally enabled and configured.
     *
     * @return bool
     */
    function is_ads_enabled(): bool
    {
        $config = get_ad_config();
        return $config['enabled'] === true;
    }
}

if (!function_exists('render_banner_slot')) {
    /**
     * Render the HTML for a banner ad container.
     * The actual ad loading is handled by ads.js via GPT.
     *
     * @param string $position  One of: home_top, home_mid, quiz_sidebar, play_result
     * @param string $extraClass  Additional CSS class
     * @return string  HTML output
     */
    function render_banner_slot(string $position, string $extraClass = ''): string
    {
        $config = get_ad_config();

        if (!$config['enabled'] || !$config['banner']['enabled']) {
            return '';
        }

        // Map position to slot path
        $slotMap = [
            'home_top'         => $config['banner']['home_slot'],
            'home_mid'         => $config['banner']['home_slot'],
            'quiz_sidebar'     => $config['banner']['quiz_slot'],
            'play_question'    => $config['banner']['quiz_slot'],
            'play_mid'         => $config['banner']['quiz_slot'],
            'play_result'      => $config['banner']['play_slot'],
            'play_bottom'      => $config['banner']['play_slot'],
            'left_skyscraper'  => $config['banner']['quiz_slot'],
            'right_skyscraper' => $config['banner']['quiz_slot'],
        ];

        $slotPath = $slotMap[$position] ?? '';
        if (empty($slotPath)) {
            return '';
        }

        // Size mapping for different positions
        $sizeMap = [
            'home_top'         => ['728', '90'],
            'home_mid'         => ['728', '90'],
            'quiz_sidebar'     => ['300', '250'],
            'play_question'    => ['300', '250'],
            'play_mid'         => ['300', '250'],
            'play_result'      => ['300', '250'],
            'play_bottom'      => ['300', '250'],
            'left_skyscraper'  => ['160', '600'],
            'right_skyscraper' => ['160', '600'],
        ];

        $size = $sizeMap[$position] ?? ['728', '90'];
        $divId = 'gam-banner-' . str_replace('_', '-', $position);

        $html  = '<div class="ad-banner-container ' . esc($extraClass) . '" id="' . esc($divId) . '-wrapper">';
        $html .= '<span class="ad-label">Advertisement</span>';
        $html .= '<div id="' . esc($divId) . '" ';
        $html .= 'data-ad-slot="' . esc($slotPath) . '" ';
        $html .= 'data-ad-width="' . esc($size[0]) . '" ';
        $html .= 'data-ad-height="' . esc($size[1]) . '" ';
        $html .= 'class="ad-banner-slot">';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
