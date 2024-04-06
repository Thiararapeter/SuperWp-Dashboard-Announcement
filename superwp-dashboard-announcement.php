<?php
/**
 * Superwp Dashboard Announcement
 *
 * @package       SUPERWPDAS
 * @author        Thiarara
 * @license       gplv2-or-later
 * @version       1.1.01
 *
 * @wordpress-plugin
 * Plugin Name:   Superwp Dashboard Announcement
 * Plugin URI:    https://github.com/Thiararapeter/Superwp-Dashboard-Announcement
 * Description:   This plugin adds an announcement to the WordPress dashboard
 * Version:       1.1.01
 * Author:        Thiarara
 * Author URI:    https://profiles.wordpress.org/thiarara
 * Text Domain:   superwp-dashboard-announcement
 * Domain Path:   /languages
 * License:       GPLv2 or later
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Superwp Dashboard Announcement. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function superwp_dashboard_announcement_dashboard_widget() {
    wp_add_dashboard_widget(
        'superwp_dashboard_announcement_dashboard_widget', // Widget slug.
        'Superwp Dashboard Announcement', // Title.
        'superwp_dashboard_announcement_dashboard_widget_content' // Display function.
    );
}
add_action('wp_dashboard_setup', 'superwp_dashboard_announcement_dashboard_widget');

    function superwp_dashboard_announcement_dashboard_widget_content() {
        // Get the current settings.
        $title = get_option('superwp_dashboard_announcement_title', '');
        $content = get_option('superwp_dashboard_announcement_content', '');
        $user_roles = get_option('superwp_dashboard_announcement_user_roles', array());
        $badge_duration = get_option('superwp_dashboard_announcement_badge_duration', 7); // Default to 7 days
        $title_font = get_option('superwp_dashboard_announcement_title_font', 'Roboto');
        $content_font = get_option('superwp_dashboard_announcement_content_font', 'Roboto');
        $title_size = get_option('superwp_dashboard_announcement_title_size', '20px');
        $content_size = get_option('superwp_dashboard_announcement_content_size', '16px');
        $title_color = get_option('superwp_dashboard_announcement_title_color', '#333');
        $content_color = get_option('superwp_dashboard_announcement_content_color', '#666');
        $title_alignment = get_option('superwp_dashboard_announcement_title_alignment', 'left');
        $content_alignment = get_option('superwp_dashboard_announcement_content_alignment', 'left');
        $background_color = get_option('superwp_dashboard_announcement_background_color', '#fff');
        $announcement_timestamp = get_option('superwp_dashboard_announcement_announcement_timestamp', 0);
        $collapsible = get_option('superwp_dashboard_announcement_collapsible', 0);
    
        // Check if there's an announcement available.
        $is_announcement_available = !empty($title) && !empty($content);
    
        // Check if the current user has one of the selected roles.
        $current_user_role = wp_get_current_user()->roles;
        $is_visible = array_intersect($current_user_role, $user_roles);
    
        // Check if the badge should be displayed.
        $badge_display = false;
        if ($announcement_timestamp > 0 && (time() - $announcement_timestamp) <= $badge_duration * 24 * 60 * 60) {
            $badge_display = true;
        }
    
        // Display the widget content only if the user has one of the selected roles.
        if (!empty($is_visible)) {
            echo '<div class="superwp-dashboard-announcement-content" style="background-color: ' . esc_attr($background_color) . '; padding: 20px; border: 1px solid #ccc;">';
            if ($is_announcement_available) {
                if ($badge_display) {
                    echo '<span class="new-announcement-badge">New Announcement</span>';
                } else {
                    echo '<span class="no-new-announcement-notice">No New Announcement</span>';
                }
                echo '<h2 style="font-family: ' . esc_attr($title_font) . '; font-size: ' . esc_attr($title_size) . '; color: ' . esc_attr($title_color) . '; text-align: ' . esc_attr($title_alignment) . '; font-weight: bold;">' . esc_html($title) . '</h2>';
                echo '<div style="font-family: ' . esc_attr($content_font) . '; font-size: ' . esc_attr($content_size) . '; color: ' . esc_attr($content_color) . '; text-align: ' . esc_attr($content_alignment) . '; font-weight: normal; border: 1px solid #ccc; padding: 10px;">' . wp_kses_post(apply_filters('the_content', $content)) . '</div>';
            } else {
                echo '<h2>No Announcement Available</h2>';
            }
            if ($announcement_timestamp > 0) {
                echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
            }
            if ($collapsible) {
                echo '<script>
                    jQuery(document).ready(function($) {
                        $(".superwp-dashboard-announcement-content").click(function() {
                            $(this).toggleClass("collapsed");
                        });
                    });
                </script>';
                echo '<style>
                    .superwp-dashboard-announcement-content.collapsed {
                        max-height: 0;
                        overflow: hidden;
                        transition: max-height 0.2s ease-out;
                    }
                </style>';
            }
            echo '</div>';
        } else {
            echo '<div class="superwp-dashboard-announcement-content" style="background-color: ' . esc_attr($background_color) . '; padding: 20px; border: 1px solid #ccc;">';
            echo '<h2>No Announcement for You</h2>';
            echo '<div>You do not have the right to view this announcement.</div>';
            echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
            echo '</div>';
        }
    }

function superwp_dashboard_announcement_add_menu() {
    add_menu_page(
        'Superwp Dashboard Announcement', // Page title.
        'S. Announcement', // Menu title.
        'manage_options', // Capability.
        'superwp-dashboard-announcement', // Menu slug.
        'superwp_dashboard_announcement_dashboard_page', // Callback function.
        'dashicons-megaphone', // Icon URL.
        2 // Position.
    );

    add_submenu_page(
        'superwp-dashboard-announcement', // Parent slug.
        'Superwp Dashboard Announcement Settings', // Page title.
        'Settings', // Menu title.
        'manage_options', // Capability.
        'superwp-dashboard-announcement-settings', // Menu slug.
        'superwp_dashboard_announcement_settings_page' // Callback function.
    );
}
add_action('admin_menu', 'superwp_dashboard_announcement_add_menu');

function superwp_dashboard_announcement_dashboard_page() {
    // Check user capabilities.
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if our form has been submitted.
    if (isset($_POST['superwp_dashboard_announcement_settings_submit'])) {
        // Check nonce for security.
        if (wp_verify_nonce($_POST['superwp_dashboard_announcement_settings_nonce'], 'superwp_dashboard_announcement_settings')) {
            // Save the settings.
            update_option('superwp_dashboard_announcement_title', sanitize_text_field($_POST['superwp_dashboard_announcement_title']));
            update_option('superwp_dashboard_announcement_content', $_POST['superwp_dashboard_announcement_content']);
            update_option('superwp_dashboard_announcement_announcement_timestamp', time());
            echo '<div id="message" class="updated notice is-dismissible"><p>Settings saved.</p></div>';
        }
    }

    // Get the current settings.
$title = get_option('superwp_dashboard_announcement_title', '');
$content = get_option('superwp_dashboard_announcement_content', '');

// Check if options were retrieved successfully
if ($title === false || $content === false) {
    // Handle the error, for example:
    $title = ''; // Set default value for title
    $content = ''; // Set default value for content
    // You can also log the error or display an error message to the user
    error_log('Error: Unable to retrieve options for title or content.');
}

// Display the settings page.
echo '<div class="wrap" style="width: 70%; margin: 0 auto;">';
echo '<h1>Superwp Dashboard Announcement</h1>';
echo '<p>Follow these steps to add a new announcement:</p>';
echo '<ol>';
echo '<li>Fill in the "Announcement Title" field with the title of your announcement.</li>';
echo '<li>Enter the announcement content in the "Announcement Content" field below.</li>';
echo '<li>Click the "Save Settings" button to save your announcement.</li>';
echo '</ol>';
echo '<form method="post" action="">';
echo '<input type="hidden" name="superwp_dashboard_announcement_settings_nonce" value="' . esc_attr(wp_create_nonce('superwp_dashboard_announcement_settings')) . '">';
echo '<p><label for="superwp_dashboard_announcement_title">Announcement Title:</label>';
echo '<input type="text" id="superwp_dashboard_announcement_title" name="superwp_dashboard_announcement_title" value="' . esc_attr($title) . '" class="regular-text"></p>';
echo '<p><label for="superwp_dashboard_announcement_content">Announcement Content:</label></p>';
wp_editor($content, 'superwp_dashboard_announcement_content', array('textarea_name' => 'superwp_dashboard_announcement_content'));
echo '<p><input type="submit" name="superwp_dashboard_announcement_settings_submit" value="Save Settings" class="button button-primary"></p>';
echo '</form>';
echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
echo '</div>';
}

function superwp_dashboard_announcement_settings_page() {
    // Check user capabilities.
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if our form has been submitted.
    if (isset($_POST['superwp_dashboard_announcement_settings_submit'])) {
        // Check nonce for security.
        if (wp_verify_nonce($_POST['superwp_dashboard_announcement_settings_nonce'], 'superwp_dashboard_announcement_settings')) {
            // Save the settings.
            $user_roles = isset($_POST['user_roles']) ? $_POST['user_roles'] : array();
            update_option('superwp_dashboard_announcement_user_roles', $user_roles);
            update_option('superwp_dashboard_announcement_badge_duration', intval($_POST['superwp_dashboard_announcement_badge_duration']));
            update_option('superwp_dashboard_announcement_title_font', sanitize_text_field($_POST['superwp_dashboard_announcement_title_font']));
            update_option('superwp_dashboard_announcement_content_font', sanitize_text_field($_POST['superwp_dashboard_announcement_content_font']));
            update_option('superwp_dashboard_announcement_title_size', sanitize_text_field($_POST['superwp_dashboard_announcement_title_size']));
            update_option('superwp_dashboard_announcement_content_size', sanitize_text_field($_POST['superwp_dashboard_announcement_content_size']));
            update_option('superwp_dashboard_announcement_title_color', sanitize_text_field($_POST['superwp_dashboard_announcement_title_color']));
            update_option('superwp_dashboard_announcement_content_color', sanitize_text_field($_POST['superwp_dashboard_announcement_content_color']));
            update_option('superwp_dashboard_announcement_title_alignment', sanitize_text_field($_POST['superwp_dashboard_announcement_title_alignment']));
            update_option('superwp_dashboard_announcement_content_alignment', sanitize_text_field($_POST['superwp_dashboard_announcement_content_alignment']));
            update_option('superwp_dashboard_announcement_background_color', sanitize_text_field($_POST['superwp_dashboard_announcement_background_color']));
            update_option('superwp_dashboard_announcement_collapsible', isset($_POST['superwp_dashboard_announcement_collapsible']) ? 1 : 0);
            echo '<div id="message" class="updated notice is-dismissible"><p>Settings saved.</p></div>';
        }
    }

    // Get all user roles.
    $user_roles = get_editable_roles();
    $selected_roles = get_option('superwp_dashboard_announcement_user_roles', array());
    $badge_duration = get_option('superwp_dashboard_announcement_badge_duration', 7);
    $title_font = get_option('superwp_dashboard_announcement_title_font', 'Roboto');
    $content_font = get_option('superwp_dashboard_announcement_content_font', 'Roboto');
    $title_size = get_option('superwp_dashboard_announcement_title_size', '20px');
    $content_size = get_option('superwp_dashboard_announcement_content_size', '16px');
    $title_color = get_option('superwp_dashboard_announcement_title_color', '#333');
    $content_color = get_option('superwp_dashboard_announcement_content_color', '#666');
    $title_alignment = get_option('superwp_dashboard_announcement_title_alignment', 'left');
    $content_alignment = get_option('superwp_dashboard_announcement_content_alignment', 'left');
    $background_color = get_option('superwp_dashboard_announcement_background_color', '#fff');

    // Display the settings page.
    echo '<div class="wrap" style="width: 50%; margin: 0 auto;">';
    echo '<h1>Superwp Dashboard Announcement Settings</h1>';
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="superwp_dashboard_announcement_settings_nonce" value="' . esc_attr(wp_create_nonce('superwp_dashboard_announcement_settings')) . '">';
    echo '<div style="display: flex; flex-wrap: wrap; justify-content: space-between;">';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Who Can View Annoucement:</h2>';
    foreach ($user_roles as $role => $details) {
        $checked = in_array($role, $selected_roles) ? 'checked' : '';
    echo '<p><input type="checkbox" name="user_roles[]" value="' . esc_attr($role) . '" ' . esc_attr($checked) . '> ' . esc_html($details['name']) . '</p>';
    }
    echo '</div>';
    echo '<div style="width: 100%;">';
    echo '<h2>Badge Display Duration (Days)</h2>';
    echo '<input type="number" name="superwp_dashboard_announcement_badge_duration" value="' . esc_attr($badge_duration) . '" min="1">';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Title Font</h2>';
    echo '<select name="superwp_dashboard_announcement_title_font">';
    echo '<option value="Roboto" ' . selected($title_font, 'Roboto', false) . '>Roboto</option>';
    echo '<option value="Open Sans" ' . selected($title_font, 'Open Sans', false) . '>Open Sans</option>';
    echo '<option value="Lato" ' . selected($title_font, 'Lato', false) . '>Lato</option>';
    echo '<option value="Montserrat" ' . selected($title_font, 'Montserrat', false) . '>Montserrat</option>';
    echo '<option value="Oswald" ' . selected($title_font, 'Oswald', false) . '>Oswald</option>';
    echo '</select>';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Content Font</h2>';
    echo '<select name="superwp_dashboard_announcement_content_font">';
    echo '<option value="Roboto" ' . selected($content_font, 'Roboto', false) . '>Roboto</option>';
    echo '<option

 value="Open Sans" ' . selected($content_font, 'Open Sans', false) . '>Open Sans</option>';
    echo '<option value="Lato" ' . selected($content_font, 'Lato', false) . '>Lato</option>';
    echo '<option value="Montserrat" ' . selected($content_font, 'Montserrat', false) . '>Montserrat</option>';
    echo '<option value="Oswald" ' . selected($content_font, 'Oswald', false) . '>Oswald</option>';
    echo '</select>';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Title Font Size</h2>';
    echo '<input type="text" name="superwp_dashboard_announcement_title_size" value="' . esc_attr($title_size) . '">';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Content Font Size</h2>';
    echo '<input type="text" name="superwp_dashboard_announcement_content_size" value="' . esc_attr($content_size) . '">';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Title Color</h2>';
    echo '<input type="text" name="superwp_dashboard_announcement_title_color" value="' . esc_attr($title_color) . '">';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Content Color</h2>';
    echo '<input type="text" name="superwp_dashboard_announcement_content_color" value="' . esc_attr($content_color) . '">';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Title Alignment</h2>';
    echo '<select name="superwp_dashboard_announcement_title_alignment">';
    echo '<option value="left" ' . selected($title_alignment, 'left', false) . '>Left</option>';
    echo '<option value="center" ' . selected($title_alignment, 'center', false) . '>Center</option>';
    echo '<option value="right" ' . selected($title_alignment, 'right', false) . '>Right</option>';
    echo '</select>';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Content Alignment</h2>';
    echo '<select name="superwp_dashboard_announcement_content_alignment">';
    echo '<option value="left" ' . selected($content_alignment, 'left', false) . '>Left</option>';
    echo '<option value="center" ' . selected($content_alignment, 'center', false) . '>Center</option>';
    echo '<option value="right" ' . selected($content_alignment, 'right', false) . '>Right</option>';
    echo '</select>';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Background Color</h2>';
    echo '<input type="text" name="superwp_dashboard_announcement_background_color" value="' . esc_attr($background_color) . '">';
    echo '</div>';
    echo '<div style="width: 100%; margin-bottom: 20px;">';
    echo '<h2>Collapsible</h2>';
    echo '<label><input type="checkbox" name="superwp_dashboard_announcement_collapsible" value="1" ' . checked(get_option('superwp_dashboard_announcement_collapsible', 0), 1, false) . '> Allow collapsible content</label>';
    echo '</div>';
    echo '<div style="width: 100%;">';
    echo '<p><input type="submit" name="superwp_dashboard_announcement_settings_submit" value="Save Settings" class="button button-primary"></p>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
    echo '</div>';
}

// Add settings and new announcement links to plugin row
function superwp_dashboard_announcement_settings_links($links) {
    // Define settings page URL
    $settings_link = admin_url('admin.php?page=superwp-dashboard-announcement-settings');
    // Define new announcement page URL
    $new_announcement_link = admin_url('admin.php?page=superwp-dashboard-announcement');

    // Add settings link next to deactivated plugin
    $settings_link_html = '<a href="' . esc_url($settings_link) . '">Settings</a>';
    array_unshift($links, $settings_link_html);

    // Add new announcement link next to deactivated plugin
    $new_announcement_link_html = '<a href="' . esc_url($new_announcement_link) . '">New Announcement</a>';
    array_unshift($links, $new_announcement_link_html);

    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'superwp_dashboard_announcement_settings_links');

function superwp_dashboard_announcement_enqueue_google_fonts() {
    $title_font = get_option('superwp_dashboard_announcement_title_font', 'Roboto');
    $content_font = get_option('superwp_dashboard_announcement_content_font', 'Roboto');

    // Ensure fonts are not duplicated.
    $fonts = array_unique(array($title_font, $content_font));

    // Convert font names to Google Fonts format.
    $google_fonts = array_map(function($font) {
        return str_replace(' ', '+', $font);
    }, $fonts);

    // Enqueue Google Fonts.
    wp_enqueue_style('superwp-dashboard-announcement-google-fonts', 'https://fonts.googleapis.com/css?family=' . implode('|', $google_fonts), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'superwp_dashboard_announcement_enqueue_google_fonts');

function superwp_dashboard_announcement_enqueue_admin_styles() {
    echo '<style>
        .superwp-dashboard-announcement-content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .superwp-dashboard-announcement-content h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .new-announcement-badge {
            background-color: #ff0000;
            color: #ffffff;
            padding: 5px 10px;
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .wrap {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .wrap h1, .wrap h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .wrap input[type="text"], .wrap textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .wrap .button {
            margin-top: 10px;
        }
    </style>';
}
add_action('admin_head', 'superwp_dashboard_announcement_enqueue_admin_styles');