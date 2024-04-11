<?php
/**
 * Superwp Dashboard Announcement
 *
 * @package       SUPERWPDAS
 * @author        Thiarara
 * @license       gplv2-or-later
 * @version       1.3.01
 *
 * @wordpress-plugin
 * Plugin Name:   Superwp Dashboard Announcement
 * Plugin URI:    https://wordpress.org/Superwp-Dashboard-Announcement
 * Description:   Superwp Dashboard Announcement is a WordPress plugin that allows you to easily add custom announcements to your WordPress dashboard for all users.
 * Version:       1.3.01
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
// Plugin name
define( 'SUPERWPDAS_NAME',			'Superwp Dashboard Announcement' );

// Plugin version
define( 'SUPERWPDAS_VERSION',		'1.3.01' );

// Plugin Root File
define( 'SUPERWPDAS_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'SUPERWPDAS_PLUGIN_BASE',	plugin_basename( SUPERWPDAS_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'SUPERWPDAS_PLUGIN_DIR',	plugin_dir_path( SUPERWPDAS_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'SUPERWPDAS_PLUGIN_URL',	plugin_dir_url( SUPERWPDAS_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once SUPERWPDAS_PLUGIN_DIR . 'core/class-superwp-dashboard-announcement.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Thiarara
 * @since   1.3.01
 * @return  object|Superwp_Dashboard_Announcement
 */
function SUPERWPDAS() {
	return Superwp_Dashboard_Announcement::instance();
}

SUPERWPDAS();

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
