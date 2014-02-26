<?php
/**
 * Plugin Name: De Olho no Plano - Agenda
 * Plugin URI:  http://deolhonoplano.org.br/
 * Description: Agenda.
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

/**
 * Initialize the plugin actions.
 */
add_action( 'plugins_loaded', array( 'DONP_Agenda', 'get_instance' ) );

/**
 * Plugin admin.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-donp-agenda-admin.php';

	add_action( 'plugins_loaded', array( 'DONP_Agenda_Admin', 'get_instance' ) );
}

function donp_calendar() {
	echo DONP_Agenda::calendar();
}
