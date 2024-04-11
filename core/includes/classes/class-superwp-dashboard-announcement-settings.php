<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Superwp_Dashboard_Announcement_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		SUPERWPDAS
 * @subpackage	Classes/Superwp_Dashboard_Announcement_Settings
 * @author		Thiarara
 * @since		1.3.01
 */
class Superwp_Dashboard_Announcement_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.3.01
	 */
	private $plugin_name;

	/**
	 * Our Superwp_Dashboard_Announcement_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.3.01
	 */
	function __construct(){

		$this->plugin_name = SUPERWPDAS_NAME;
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.3.01
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'SUPERWPDAS/settings/get_plugin_name', $this->plugin_name );
	}
}