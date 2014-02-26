<?php
/**
 * DONP_Agenda_Admin class.
 */
class DONP_Agenda_Admin {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
		add_action( 'admin_init', array( $this, 'metabox' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load scripts in front-end.
	 */
	public function scripts() {
		$screen = get_current_screen();

		if ( 'agenda' === $screen->id ) {
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			wp_enqueue_style( 'jquery-ui-agenda-styles', plugins_url( 'assets/css/agenda-jquery-ui-styles.css', plugin_dir_path( dirname( __FILE__ ) ) ), array(), '' );
			wp_enqueue_script( 'agenda-calendar', plugins_url( 'assets/js/agenda-admin-metabox.js', plugin_dir_path( dirname( __FILE__ ) ) ), array( 'jquery' ), '', true );
		}
	}

	/**
	 * Add metabox.
	 */
	public function metabox() {
		add_meta_box(
			'donp-agenda-metabox',
			__( 'Detalhes do evento', 'donp-agenda' ),
			array( $this, 'metabox_content' ),
			'agenda',
			'advanced',
			'core'
		);
	}

	/**
	 * Metabox content.
	 */
	public function metabox_content( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'donp-agenda' );

		// Start date.
		$event_start                 = get_post_meta( $post->ID, 'event_start', true );
		$current_event_start_date    = '';
		$current_event_start_hour    = '';
		$current_event_start_minutes = '';
		if ( ! empty( $event_start ) ) {
			$_current_event_start        = new DateTime( date( 'd-m-Y H:i', $event_start ) );
			$current_event_start_date    = $_current_event_start->format( 'd/m/Y' );
			$current_event_start_hour    = $_current_event_start->format( 'H' );
			$current_event_start_minutes = $_current_event_start->format( 'i' );
		}

		// End date.
		$event_end                 = get_post_meta( $post->ID, 'event_end', true );
		$current_event_end_date    = '';
		$current_event_end_hour    = '';
		$current_event_end_minutes = '';
		if ( ! empty( $event_end ) ) {
			$_current_event_end        = new DateTime( date( 'd-m-Y H:i', $event_end ) );
			$current_event_end_date    = $_current_event_end->format( 'd/m/Y' );
			$current_event_end_hour    = $_current_event_end->format( 'H' );
			$current_event_end_minutes = $_current_event_end->format( 'i' );
		}

		include_once plugin_dir_path( __FILE__ ) . 'views/html-metabox.php';
	}

	protected function fix_date( $date ) {
		return date( 'Y-m-d', strtotime( str_replace( '/', '-', $date ) ) );
	}

	/**
	 * Save metabox.
	 */
	public function save_metabox( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['donp-agenda'] ) || ! wp_verify_nonce( $_POST['donp-agenda'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Verify if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check permissions.
		if ( 'agenda' != $_POST['post_type'] || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['event_start_date'] ) ) {
			$_event_start_date = $this->fix_date( $_POST['event_start_date'] );
			$_event_start_hour = isset( $_POST['event_start_hour'] ) ? $_POST['event_start_hour'] : '00';
			$_event_start_minutes = isset( $_POST['event_start_minutes'] ) ? $_POST['event_start_minutes'] : '00';

			$event_start_date = strtotime( $_event_start_date . ' ' . $_event_start_hour . ':' . $_event_start_minutes );
			update_post_meta( $post_id, 'event_start', $event_start_date );
		}

		if ( isset( $_POST['event_end_date'] ) ) {
			$_event_end_date = $this->fix_date( $_POST['event_end_date'] );
			$_event_end_hour = isset( $_POST['event_end_hour'] ) ? $_POST['event_end_hour'] : '00';
			$_event_end_minutes = isset( $_POST['event_end_minutes'] ) ? $_POST['event_end_minutes'] : '00';

			$event_end_date = strtotime( $_event_end_date . ' ' . $_event_end_hour . ':' . $_event_end_minutes );
			update_post_meta( $post_id, 'event_end', $event_end_date );
		}
	}

}
