<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Superwp_Dashboard_Announcement' ) ) :

	/**
	 * Main Superwp_Dashboard_Announcement Class.
	 *
	 * @package		SUPERWPDAS
	 * @subpackage	Classes/Superwp_Dashboard_Announcement
	 * @since		1.3.01
	 * @author		Thiarara
	 */
	final class Superwp_Dashboard_Announcement {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.3.01
		 * @var		object|Superwp_Dashboard_Announcement
		 */
		private static $instance;

		/**
		 * SUPERWPDAS helpers object.
		 *
		 * @access	public
		 * @since	1.3.01
		 * @var		object|Superwp_Dashboard_Announcement_Helpers
		 */
		public $helpers;

		/**
		 * SUPERWPDAS settings object.
		 *
		 * @access	public
		 * @since	1.3.01
		 * @var		object|Superwp_Dashboard_Announcement_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.3.01
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to clone this class.', 'superwp-dashboard-announcement' ), '1.3.01' );
		}				

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.3.01
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to unserialize this class.', 'superwp-dashboard-announcement' ), '1.3.01' );
		}		

		/**
		 * Main Superwp_Dashboard_Announcement Instance.
		 *
		 * Insures that only one instance of Superwp_Dashboard_Announcement exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.3.01
		 * @static
		 * @return		object|Superwp_Dashboard_Announcement	The one true Superwp_Dashboard_Announcement
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Superwp_Dashboard_Announcement ) ) {
				self::$instance					= new Superwp_Dashboard_Announcement;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Superwp_Dashboard_Announcement_Helpers();
				self::$instance->settings		= new Superwp_Dashboard_Announcement_Settings();

				//Fire the plugin logic
				new Superwp_Dashboard_Announcement_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'SUPERWPDAS/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.3.01
		 * @return  void
		 */
		private function includes() {
			require_once SUPERWPDAS_PLUGIN_DIR . 'core/includes/classes/class-superwp-dashboard-announcement-helpers.php';
			require_once SUPERWPDAS_PLUGIN_DIR . 'core/includes/classes/class-superwp-dashboard-announcement-settings.php';

			require_once SUPERWPDAS_PLUGIN_DIR . 'core/includes/classes/class-superwp-dashboard-announcement-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.3.01
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.3.01
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'superwp-dashboard-announcement', false, dirname( plugin_basename( SUPERWPDAS_PLUGIN_FILE ) ) . '/languages/' );
		}		
	}

	function superwp_dashboard_announcement_dashboard_widget() {
		wp_add_dashboard_widget(
			'superwp_dashboard_announcement_dashboard_widget', // Widget slug.
			'Superwp Dashboard Announcement', // Title.
			'superwp_dashboard_announcement_dashboard_widget_content' // Display function.
		);
	}
	add_action('wp_dashboard_setup', 'superwp_dashboard_announcement_dashboard_widget');
	
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
	
	// Add submenu page for feedback
	function superwp_dashboard_announcement_add_feedback_page() {
		add_submenu_page(
			'superwp-dashboard-announcement', // Parent slug.
			'Feedback', // Page title.
			'Feedback', // Menu title.
			'manage_options', // Capability.
			'superwp-dashboard-announcement-feedback', // Menu slug.
			'superwp_dashboard_announcement_feedback_page' // Callback function.
		);
	}
	add_action('admin_menu', 'superwp_dashboard_announcement_add_feedback_page');
	
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
		$badge_name = get_option('superwp_dashboard_announcement_badge_name', 'New Announcement'); // New option for badge name
	
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
			echo '<div class="dashboard-widget-container">'; // Opening container div
			echo '<div id="superwp-dashboard-widget" class="superwp-dashboard-announcement-content" style="background-color: ' . esc_attr($background_color) . '; padding: 20px; border: 1px solid #ccc; text-align: left;">';
			if ($is_announcement_available) {
        if ($badge_display) {
            echo '<span class="new-announcement-badge">' . esc_html($badge_name) . '</span>'; // Display badge name
        } else {
            // Ensure the background color for the badge is defined
            $badge_background_color = get_option('superwp_dashboard_announcement_badge_background_color', '#ff0000'); // Default to red color
            echo '<span class="no-new-announcement-notice" style="background-color: ' . esc_attr($badge_background_color) . '; border: 1px solid #ccc; padding: 5px 10px; border-radius: 50px; color: #ffcccc;">Announcement posted ' . esc_html(human_time_diff($announcement_timestamp, current_time('timestamp'))) . ' ago</span>';
        }
		echo '<h2 style="font-family: ' . esc_attr($title_font) . '; font-size: ' . esc_attr($title_size) . '; color: ' . esc_attr($title_color) . '; text-align: ' . esc_attr($title_alignment) . '; font-weight: bold; margin-top: 20px;">' . esc_html(stripslashes($title)) . '</h2>';
        // Apply font family to the content directly
        echo '<div style="font-size: ' . esc_attr($content_size) . '; color: ' . esc_attr($content_color) . '; text-align: ' . esc_attr($content_alignment) . '; font-weight: normal; font-family: ' . esc_attr($content_font) . '; border: 1px solid #ccc; padding: 10px;">' . wp_kses_post(stripslashes($content)) . '</div>';    
        // Display the timestamp (date and time)
        echo '<p style="margin-top: 10px;">Posted on: ' . esc_html(gmdate("F j, Y, g:i a", $announcement_timestamp)) . '</p>';
	
		// Feedback section
		echo '<div class="feedback-section" style="margin-top: 20px;">';
		echo '<h3 style="font-size: 14px; margin-bottom: 10px;">Share Your Feedback:</h3>';
		echo '<form method="post">';

		// Place the select dropdown before the textarea
		echo '<div style="margin-top: 10px;">';
		echo '<select name="feedback_reaction" id="feedback_reaction" style="padding: 3px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; width: 30%; display: block;">';
		echo '<option value="like">Like</option>';
		echo '<option value="dislike">Dislike</option>';
		echo '</select>';
		echo '</div>';

			// Textarea for entering feedback message
			echo '<textarea name="feedback_message" id="feedback_message" placeholder="Type your feedback here" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;" rows="4"></textarea>';

			// Submit button for the form
			echo '<button type="submit" name="submit_feedback" id="submit_feedback" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px;">Submit Feedback</button>';

			echo '</form>';
			echo '</div>';  // Close feedback section div

			} else {
				echo '<h2>No Announcement Available</h2>';
			}
			echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
			echo '</div>'; // Close superwp-dashboard-widget
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
			echo '</div>'; // Closing container div content not adjusting
		
			// Handle feedback submission
			if (isset($_POST['submit_feedback'])) {
				// Check if message length is less than 3 characters
				$feedback_message = isset($_POST['feedback_message']) ? trim($_POST['feedback_message']) : '';
				if (strlen($feedback_message) < 3) {
					// Display error notice if message is too short
					echo '<div class="notice notice-error is-dismissible"><p>Feedback message should be at least 3 characters long.</p></div>';
					return; // Stop further execution
				}
				// Proceed with feedback submission
				$feedback_data = array(
					'announcement_title' => $title,
					'comment' => $feedback_message,
					'user' => wp_get_current_user()->user_login,
					'reaction' => $_POST['feedback_reaction'],
					'time' => time()
				);
				// Save feedback data
				$existing_feedback = get_option('superwp_dashboard_announcement_feedback', array());
				$existing_feedback[] = $feedback_data;
				update_option('superwp_dashboard_announcement_feedback', $existing_feedback);
				// Clear feedback field
				echo '<script>document.getElementById("feedback_message").value = "";</script>';
				// Display success notice dynamically
				echo '<div id="feedback-success-notice" class="notice notice-success is-dismissible"><p>Feedback submitted successfully!</p></div>';
			}
		} else {
			echo '<div class="superwp-dashboard-announcement-content" style="background-color: ' . esc_attr($background_color) . '; padding: 20px; border: 1px solid #ccc;">';
			echo '<h2>No Announcement for You</h2>';
			echo '<div>You Currently dont have the right to view this announcement.</div>';
			echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
			echo '</div>';
		}
	}
	
	function superwp_dashboard_announcement_feedback_page() {
		// Check user capabilities.
		if (!current_user_can('manage_options')) {
			return;
		}
	
		// Handle Deleting All Feedback
		if (isset($_POST['delete_all_feedback'])) {
			delete_option('superwp_dashboard_announcement_feedback');
			wp_redirect(admin_url('admin.php?page=superwp-dashboard-announcement-feedback'));
			exit;
		}
	
		// Handle Deleting Selected Feedback
		if (isset($_POST['delete_selected_feedback'])) {
			$selected_entries = isset($_POST['feedback_entry']) ? $_POST['feedback_entry'] : array();
			$feedback_entries = get_option('superwp_dashboard_announcement_feedback', array());
			foreach ($selected_entries as $key) {
				if (isset($feedback_entries[$key])) {
					unset($feedback_entries[$key]);
				}
			}
			update_option('superwp_dashboard_announcement_feedback', $feedback_entries);
			wp_redirect(admin_url('admin.php?page=superwp-dashboard-announcement-feedback'));
			exit;
		}
	
		// Fetch feedback data
		$feedback_data = get_option('superwp_dashboard_announcement_feedback', array());
	
				
		// Output buffering to capture HTML content.
		ob_start();
		?>
		<div class="wrap">
			<h1>Feedback</h1>
			<?php if (!empty($feedback_data)) : ?>
				<form method="post">
					<table class="wp-list-table widefat striped">
						<thead>
							<tr>
								<th class="manage-column">Select</th>
								<th class="manage-column">Announcement Title</th>
								<th class="manage-column">Comment</th>
								<th class="manage-column">User</th>
								<th class="manage-column">Reaction</th>
								<th class="manage-column">Time</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($feedback_data as $key => $feedback) : ?>
							<tr>
							<td><input type="checkbox" name="feedback_entry[]" value="<?php echo esc_attr($key); ?>"></td>
								<td title="<?php echo esc_attr(stripslashes($feedback['announcement_title'])) ?>">
								<?php echo esc_html(strlen($feedback['announcement_title']) > 30 ? substr($feedback['announcement_title'], 0, 30) . '...' : $feedback['announcement_title']); ?>									<?php if (strlen(stripslashes($feedback['announcement_title'])) > 30) : ?>
										<strong style="color: red;">(Hover To View Full Title)</strong>
									<?php endif; ?>
							</td>
							<td title="<?php echo esc_attr(strlen($feedback['comment']) > 40 ? stripslashes($feedback['comment']) : ''); ?>">
								<?php echo esc_html(substr($feedback['comment'], 0, 40)); ?>
								<?php if (strlen($feedback['comment']) > 40) : ?>
									<strong style="color: red;">...(Hover To View Full Comment)</strong>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html($feedback['user']); ?></td>
							<td><?php echo esc_html($feedback['reaction']); ?></td>
							<td><?php echo esc_html(gmdate('Y-m-d H:i:s', $feedback['time'])); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					</table>
					<button type="submit" name="delete_selected_feedback" id="delete_selected_feedback" class="button button-primary">Delete Selected</button>
					<button type="submit" name="delete_all_feedback" id="delete_all_feedback" class="button button-primary">Wipe All</button>
				</form>
			<?php else : ?>
				<p>No feedback available.</p>
			<?php endif; ?>
		</div>

		<div class="feedbackwrap">
				<ul class="instructions">
					<li>This page displays feedback received from users. You can view each feedback entry, including the announcement title, comment, user, reaction, and time.</li>
					<li>To see the full comment, hover over the truncated comment in the list.</li>
					<li>Use the checkboxes to select feedback entries for deletion.</li>
					<li>Click 'Delete Selected' to remove selected feedback entries.</li>
					<li>Click 'Wipe All' to delete all feedback entries.</li>
				</ul>
				
			</div>

		<?php
		// Get the output content and clean the buffer.
		$output = ob_get_clean();

		// Output the generated HTML.
		echo $output;
		
		// Add the footer notice
			echo '<p style="text-align: center; margin-top: 20px;">Plugin created by Creative Designer Ke &copy; ' . esc_html(gmdate('Y')) . '</p>';
	}
	
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
				update_option('superwp_dashboard_announcement_badge_name', sanitize_text_field($_POST['superwp_dashboard_announcement_badge_name'])); // Save badge name option
				echo '<div id="message" class="updated notice is-dismissible"><p>Announcement Published. View Your Dashboard Widget For More.';

				// Check if the "Superwp Dashboard Announcement" screen option is visible
				if (!get_user_meta(get_current_user_id(), 'metaboxhidden_dashboard', true) || !in_array('superwp_dashboard_announcement', get_user_meta(get_current_user_id(), 'metaboxhidden_dashboard', true))) {
					echo ' If not visible, open Screen Options and check "Superwp Dashboard Announcement".';
				}
				echo '</p></div>';
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
	
		// Get the badge name option
		$badge_name = get_option('superwp_dashboard_announcement_badge_name', 'New Announcement');
	
		// Display the settings page.
		echo '<div class="wrap" style="width: 70%; margin: 0 auto;">';
		echo '<h1>Superwp Dashboard Announcement</h1>';
		echo '<p>Follow these steps to add a new announcement:</p>';
		echo '<ol>';
		echo '<li>Fill in the "Announcement Title" field with the title of your announcement.</li>';
		echo '<li>Enter the announcement content in the "Announcement Content" field below.</li>';
		echo '<li>Enter the badge name in the "Badge Name" field below (Optional).</li>'; // Add instructions for badge name
		echo '<li>Click the "Save Settings" button to save your announcement.</li>';
		echo '</ol>';
		echo '<div class="announcement-container">';
		echo '<p>If the "Superwp Dashboard Announcement widget" not visible in your wp admin dashboard, open Screen Options and check it.</p>';
		echo '<ul>';
		echo '<li>Make sure you have the required permissions to access this feature.</li>';
		echo '<li>If you have the permissions and still don\'t see it, check the Screen Options menu located at the top right corner of the screen.</li>';
		echo '<li>If "Superwp Dashboard Announcement" is not listed there, please contact your administrator to ensure the plugin is properly installed and activated.</li>';
		echo '</ul>';
		echo '</div>';
		echo '<form method="post" action="">';
		echo '<input type="hidden" name="superwp_dashboard_announcement_settings_nonce" value="' . esc_attr(wp_create_nonce('superwp_dashboard_announcement_settings')) . '">';

		// Decode HTML entities and remove additional slashes for the title
		$title_value = htmlspecialchars_decode(stripslashes($title));
		echo '<p><strong><label for="superwp_dashboard_announcement_title">Announcement Title:</label></strong>';
		echo '<input type="text" id="superwp_dashboard_announcement_title" name="superwp_dashboard_announcement_title" value="' . esc_attr($title_value) . '" class="regular-text"></p>';

		echo '<p><strong><label for="superwp_dashboard_announcement_badge_name">Badge Name (Optional):</label></strong>'; // Add label for badge name
		echo '<input type="text" id="superwp_dashboard_announcement_badge_name" name="superwp_dashboard_announcement_badge_name" value="' . esc_attr($badge_name) . '" class="regular-text"></p>'; // Add input field for badge name

		echo '<p><strong><label for="superwp_dashboard_announcement_content">Announcement Content:</label></strong></p>';
		// Decode HTML entities and remove additional slashes for the content
		$content_value = htmlspecialchars_decode(stripslashes($content));
		// Allow images in the WordPress editor
		$settings = array(
			'textarea_name' => 'superwp_dashboard_announcement_content',
			'media_buttons' => true, // Enable media buttons for inserting images
		);
		wp_editor($content_value, 'superwp_dashboard_announcement_content', $settings);

		echo '<p><input type="submit" name="superwp_dashboard_announcement_settings_submit" value="Publish Announcement." class="button button-primary"></p>';
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
	
				// Display a success message.
				echo '<div id="message" class="updated notice is-dismissible"><p> Annoucement Updated.</p></div>';
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
		echo '<div style="text-align: center;"><p style="background-color: green; color: white; font-weight: bold; font-size: 16px; width: 60%; margin: 0 auto;"><strong>Note:</strong> These settings will apply to the dashboard widget.</p></div>'; // Help notice
		echo '<form method="post" action="">';
		echo '<input type="hidden" name="superwp_dashboard_announcement_settings_nonce" value="' . esc_attr(wp_create_nonce('superwp_dashboard_announcement_settings')) . '">';
		echo '<div style="display: flex; flex-wrap: wrap; justify-content: space-between;">';
		echo '<div style="width: 100%; margin-bottom: 20px;">';
		echo '<h2>Who Can View Announcement:</h2>';
		foreach ($user_roles as $role => $details) {
			$checked = in_array($role, $selected_roles) ? 'checked' : '';
			echo '<p><input type="checkbox" name="user_roles[]" value="' . esc_attr($role) . '" ' . esc_attr($checked) . '> ' . esc_html($details['name']) . '</p>';
		}
		echo '</div>';	
		echo '<div style="width: 30%;">';
		echo '<h2>Badge Display Duration (Days)</h2>';
		echo '<input type="number" name="superwp_dashboard_announcement_badge_duration" value="' . esc_attr($badge_duration) . '" min="1">';
		echo '</div>';
		echo '<div style="width: 30%; margin-bottom: 20px;">';
		echo '<h2>Title Font</h2>';
		echo '<select name="superwp_dashboard_announcement_title_font">';
		echo '<option value="Roboto" ' . selected($title_font, 'Roboto', false) . '>Roboto</option>';
		echo '<option value="Open Sans" ' . selected($title_font, 'Open Sans', false) . '>Open Sans</option>';
		echo '<option value="Lato" ' . selected($title_font, 'Lato', false) . '>Lato</option>';
		echo '<option value="Montserrat" ' . selected($title_font, 'Montserrat', false) . '>Montserrat</option>';
		echo '<option value="Oswald" ' . selected($title_font, 'Oswald', false) . '>Oswald</option>';
		echo '</select>';
		echo '</div>';
		echo '<div style="width: 30%; margin-bottom: 20px;">';
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
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Title Font Size</h2>';
		echo '<input type="text" name="superwp_dashboard_announcement_title_size" value="' . esc_attr($title_size) . '">';
		echo '</div>';
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Content Font Size</h2>';
		echo '<input type="text" name="superwp_dashboard_announcement_content_size" value="' . esc_attr($content_size) . '">';
		echo '</div>';
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Title Color</h2>';
		echo '<input type="text" name="superwp_dashboard_announcement_title_color" value="' . esc_attr($title_color) . '">';
		echo '</div>';
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Content Color</h2>';
		echo '<input type="text" name="superwp_dashboard_announcement_content_color" value="' . esc_attr($content_color) . '">';
		echo '</div>';
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Title Alignment</h2>';
		echo '<select name="superwp_dashboard_announcement_title_alignment">';
		echo '<option value="left" ' . selected($title_alignment, 'left', false) . '>Left</option>';
		echo '<option value="center" ' . selected($title_alignment, 'center', false) . '>Center</option>';
		echo '<option value="right" ' . selected($title_alignment, 'right', false) . '>Right</option>';
		echo '</select>';
		echo '</div>';
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Content Alignment</h2>';
		echo '<select name="superwp_dashboard_announcement_content_alignment">';
		echo '<option value="left" ' . selected($content_alignment, 'left', false) . '>Left</option>';
		echo '<option value="center" ' . selected($content_alignment, 'center', false) . '>Center</option>';
		echo '<option value="right" ' . selected($content_alignment, 'right', false) . '>Right</option>';
		echo '</select>';
		echo '</div>';
		echo '<div style="width: 40%; margin-bottom: 20px;">';
		echo '<h2>Background Color</h2>';
		echo '<input type="text" name="superwp_dashboard_announcement_background_color" value="' . esc_attr($background_color) . '">';
		echo '</div>';
		echo '<div style="width: 50%; margin-bottom: 20px;">';
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
				padding: 5px 20px;
				position: absolute;
				top: 10px;
				right: 20px;
				border-radius: 5px;
				font-size: 14px;
				font-weight: bold;
				margin-bottom: 10px; /* Add margin-bottom here */
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
			
			/* Styling for the feedback wrap */
			.feedbackwrap {
				max-width: 800px;
				margin: 0 auto;
				padding: 20px;
				background-color: #f9f9f9;
				border-radius: 5px;
				box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
				margin-top: 40px; /* Add margin to the top */
			}
			
			/* Styling for the instructions list */
			.instructions {
				list-style-type: none;
				padding: 0;
			}

			/* Styling for each instruction item */
			.instructions li {
				margin-bottom: 10px; /* Add margin at the bottom of each item */
				font-size: 16px;
				line-height: 1.5;
			}

			/* Styling for the first instruction item */
			.instructions li:first-child {
				font-weight: bold; /* Make the first item bold */
			}

			/* Styling for the last instruction item */
			.instructions li:last-child {
				font-style: italic; /* Make the last item italic */
			}

			.announcement-container {
				color: red;
				font-weight: bold;
			}			
		</style>';
	}
	add_action('admin_head', 'superwp_dashboard_announcement_enqueue_admin_styles');
endif; // End if class_exists check.