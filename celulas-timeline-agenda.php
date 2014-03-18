<?php
/**
 * Plugin Name: Celulas - Agenda
 * Plugin URI:  http://moverjuntos.org/
 * Description: Agenda com timeline
 * Version:     1.0.0
 * Author:      Brasa
 * Author URI:  http://brasa.art.br/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin main class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-donp-agenda.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-donp-thumbnail.php';

/**
 * Initialize the plugin actions.
 */
add_action( 'plugins_loaded', array( 'DONP_Agenda', 'get_instance' ) );

/**
 * Register activate.
 */
register_activation_hook( __FILE__, array( 'DONP_Agenda', 'activate' ) );

/**
 * Plugin admin.
 */
if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-donp-agenda-admin.php';

	add_action( 'plugins_loaded', array( 'DONP_Agenda_Admin', 'get_instance' ) );
}

function donp_calendar() {
	echo DONP_Agenda::calendar();
}

function donp_timeline() {
	echo DONP_Agenda::timeline();
}
