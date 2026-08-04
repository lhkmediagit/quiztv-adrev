<?php

/**
 * Ad Helper
 * Provides utility functions for rendering Google Ad Manager ads
 * across all QuizTv views. Loaded globally via BaseController.
 *
 * Ad positions match NDTV Trivia's exact slot architecture:
 * Desktop: TopBanner, LeftRail, RightRail, MiddleDesktop, BottomDesktop
 * Mobile:  StickyTopMobile, MiddleMobile, BottomTopMobile, BottomMobile, StickyBottomMobile
 * Shared:  PlayResult
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
     * Positions match NDTV Trivia slot architecture:
     *   sticky_top_mobile, left_rail, right_rail, middle_desktop,
     *   middle_mobile, bottom_top_mobile, bottom_desktop, bottom_mobile,
     *   sticky_bottom_mobile, play_result
     *
     * @param string $position     One of the NDTV-mapped position keys
     * @param string $extraClass   Additional CSS class for responsive visibility
     * @return string              HTML output
     */
    function render_banner_slot(string $position, string $extraClass = ''): string
    {
        $config = get_ad_config();

        if (!$config['enabled'] || !$config['banner']['enabled']) {
            return '';
        }

        $b = $config['banner'];

        // Map position to slot path (using specific position override if set, falling back to default slot)
        $slotMap = [
            // Mobile-only positions
            'sticky_top_mobile'    => !empty($b['sticky_top_mobile_slot'])    ? $b['sticky_top_mobile_slot']    : $b['quiz_slot'],
            'middle_mobile'        => !empty($b['middle_mobile_slot'])        ? $b['middle_mobile_slot']        : $b['quiz_slot'],
            'bottom_top_mobile'    => !empty($b['bottom_top_mobile_slot'])    ? $b['bottom_top_mobile_slot']    : $b['play_slot'],
            'bottom_mobile'        => !empty($b['bottom_mobile_slot'])        ? $b['bottom_mobile_slot']        : $b['play_slot'],
            'sticky_bottom_mobile' => !empty($b['sticky_bottom_mobile_slot']) ? $b['sticky_bottom_mobile_slot'] : $b['quiz_slot'],
            // Desktop-only positions
            'left_rail'            => !empty($b['left_rail_slot'])            ? $b['left_rail_slot']            : $b['quiz_slot'],
            'right_rail'           => !empty($b['right_rail_slot'])           ? $b['right_rail_slot']           : $b['quiz_slot'],
            'middle_desktop'       => !empty($b['middle_desktop_slot'])       ? $b['middle_desktop_slot']       : $b['quiz_slot'],
            'bottom_desktop'       => !empty($b['bottom_desktop_slot'])       ? $b['bottom_desktop_slot']       : $b['play_slot'],
            // Shared positions
            'play_result'          => $b['play_slot'],
            // Legacy support
            'home_top'             => $b['home_slot'],
            'home_mid'             => $b['home_slot'],
            'quiz_sidebar'         => $b['quiz_slot'],
        ];

        $slotPath = $slotMap[$position] ?? '';
        if (empty($slotPath)) {
            return '';
        }

        // Size mapping matching NDTV Trivia dimensions
        $sizeMap = [
            'sticky_top_mobile'    => ['320', '50'],
            'left_rail'            => ['160', '600'],
            'right_rail'           => ['160', '600'],
            'middle_desktop'       => ['336', '280'],
            'middle_mobile'        => ['300', '250'],
            'bottom_top_mobile'    => ['300', '250'],
            'bottom_desktop'       => ['728', '90'],
            'bottom_mobile'        => ['300', '250'],
            'sticky_bottom_mobile' => ['320', '50'],
            'play_result'          => ['336', '280'],
            'home_top'             => ['728', '90'],
            'home_mid'             => ['336', '280'],
            'quiz_sidebar'         => ['300', '250'],
        ];

        $size = $sizeMap[$position] ?? ['728', '90'];
        $divId = 'gam-banner-' . str_replace('_', '-', $position);

        $html  = '<div class="ad-banner-container ' . esc($extraClass) . '" id="' . esc($divId) . '-wrapper" data-ad-empty="true" data-ad-id="' . esc($position) . '">';
        $html .= '<span class="ad-label">Advertisement</span>';
        $html .= '<div id="' . esc($divId) . '" ';
        $html .= 'data-ad-slot="' . esc($slotPath) . '" ';
        $html .= 'data-ad-width="' . esc($size[0]) . '" ';
        $html .= 'data-ad-height="' . esc($size[1]) . '" ';
        $html .= 'class="ad-banner-slot">';
        $html .= '</div>';
        $html .= '<div class="Advertisement_bottom__ROuWn" data-show-label="true"></div>';
        $html .= '</div>';

        return $html;
    }
}
