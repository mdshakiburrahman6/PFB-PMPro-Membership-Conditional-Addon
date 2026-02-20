<?php
/*
Plugin Name: PFB PMPro Membership Conditional Addon
Description: Adds "User Membership Plan" as a conditional logic source to Pure Form Builder (PMPro).
Author: Md Shakibur Rahman
Author URI: https://github.com/mdshakiburrahman6/
Version: 2.0.0
*/

if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', function () {

    if (!defined('PFB_PATH')) {
        return;
    }

    add_filter('pfb_conditional_sources', 'pfb_pmpro_add_membership_source', 10, 2);
});


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
        $options[] = trim($level->name);
    }

    $fields[] = (object) [
        'name'    => '__user_membership_plan__',
        'label'   => 'User Membership Plan',
        'options' => wp_json_encode($options),
    ];

    return $fields;
}


add_filter('pfb_resolve_field_value', function ($value, $field_name, $entry_id, $user_id) {

    if ($field_name !== '__user_membership_plan__') {
        return $value;
    }

    if (!$user_id || !function_exists('pmpro_getMembershipLevelForUser')) {
        return '';
    }

    $level = pmpro_getMembershipLevelForUser($user_id);

    return $level ? trim($level->name) : '';

}, 10, 4);