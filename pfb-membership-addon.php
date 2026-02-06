<?php
/*
Plugin Name: PFB PMPro Membership Conditional Addon
Description: Adds "User Membership Plan" as a conditional logic source to Pure Form Builder (PMPro).
Author: Md Shakibur Rahman
Author URI: https://github.com/mdshakiburrahman6/
Version: 1.1.0
*/

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Run only if Pure Form Builder is active
 */
add_action('plugins_loaded', function () {

    if (!defined('PFB_PATH')) {
        return;
    }

    // Register conditional source
    add_filter('pfb_conditional_sources', 'pfb_pmpro_add_membership_source', 10, 2);

});

/**
 * Inject "User Membership Plan" into Conditional Logic dropdown
 */
function pfb_pmpro_add_membership_source($fields, $form_id) {

    if (!function_exists('pmpro_getAllLevels')) {
        return $fields;
    }

    $levels = pmpro_getAllLevels();
    if (empty($levels)) {
        return $fields;
    }

    $options = [];

    foreach ($levels as $level) {
        $options[] = $level->name;
    }

    $fields[] = (object) [
        'name'    => '__user_membership_plan__',
        'label'   => 'User Membership Plan',
        'options' => wp_json_encode($options),
    ];

    return $fields;
}

/**
 * Resolve Membership Plan value during conditional evaluation
 */
add_filter('pfb_resolve_field_value', function ($value, $field_name, $entry_id, $user_id) {

    if ($field_name !== '__user_membership_plan__') {
        return $value;
    }

    if (!$user_id || !function_exists('pmpro_getMembershipLevelForUser')) {
        return 'Free';
    }

    $level = pmpro_getMembershipLevelForUser($user_id);

    return $level ? $level->name : 'Free';

}, 10, 4);

/**
 * DEBUG helper (safe to remove later)
 */
add_action('init', function () {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('✅ PFB PMPro Membership Conditional Addon Loaded');
    }
});
