<?php

// If uninstall.php is not called by WordPress, die.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete all options associated with the plugin.
delete_option('superwp_dashboard_announcement_title');
delete_option('superwp_dashboard_announcement_content');
delete_option('superwp_dashboard_announcement_user_roles');
delete_option('superwp_dashboard_announcement_badge_duration');
delete_option('superwp_dashboard_announcement_title_font');
delete_option('superwp_dashboard_announcement_content_font');
delete_option('superwp_dashboard_announcement_title_size');
delete_option('superwp_dashboard_announcement_content_size');
delete_option('superwp_dashboard_announcement_title_color');
delete_option('superwp_dashboard_announcement_content_color');
delete_option('superwp_dashboard_announcement_title_alignment');
delete_option('superwp_dashboard_announcement_content_alignment');
delete_option('superwp_dashboard_announcement_background_color');
delete_option('superwp_dashboard_announcement_collapsible');
delete_option('superwp_dashboard_announcement_announcement_timestamp');