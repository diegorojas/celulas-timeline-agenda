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
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'save_post', array( $this, 'save_metabox' ) );
			add_action( 'admin_init', array( $this, 'metabox' ) );
			add_action( 'agenda-categoria_add_form_fields', array( $this, 'taxonomy_add_fields' ) );
			add_action( 'agenda-categoria_edit_form_fields', array( $this, 'taxonomy_edit_fields' ), 10, 2 );
			add_action( 'edited_agenda-categoria', array( $this, 'taxonomy_save_fields' ) );
			add_filter( 'manage_edit-agenda_columns', array( $this, 'agenda_cpt_columns' ) );
			add_filter( 'manage_agenda_posts_custom_column', array( $this, 'agenda_cpt_manage_columns' ), 10, 2 );
			add_filter( 'manage_edit-agenda-categoria_columns', array( $this, 'agenda_taxonomy_columns' ) );
			add_filter( 'manage_agenda-categoria_custom_column', array( $this, 'agenda_taxonomy_manage_columns' ), 10, 3 );
		}

		add_action( 'create_agenda-categoria', array( $this, 'taxonomy_save_fields' ) );
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

		if ( 'agenda-categoria' === $screen->taxonomy ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'agenda-taxonomy', plugins_url( 'assets/js/agenda-taxonomy.js', plugin_dir_path( dirname( __FILE__ ) ) ), array( 'jquery', 'wp-color-picker' ), '', true );
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

	public function taxonomy_add_fields( $taxonomy ) {
		$html = '<div class="form-field">';
			$html .= '<label for="category-color" style="display: block;">' . __( 'Cor', 'donp-agenda' ) . '</label>';
			$html .= '<input type="text" style="display: block;" value="#ffffff" id="category-color" class="regular-text" name="category_color">';
		$html .= '</div>';

		echo $html;
	}

	public function taxonomy_edit_fields( $term, $taxonomy ) {
		$options = get_option( 'agenda_category_meta' );
		$current = ( is_array( $options ) && isset( $options[ $term->term_id ] ) ) ? $options[ $term->term_id ] : '#ffffff';

		$html = '<tr class="form-field">';
			$html .= '<th valign="top" scope="row"><label for="category-color">' . __( 'Cor', 'donp-agenda' ) . '</label></th>';
			$html .= '<td><input type="text" value="' . esc_attr( $current ) . '" id="category-color" class="regular-text" name="category_color"></td>';
		$html .= '</tr>';

		echo $html;
	}

	public function taxonomy_save_fields( $term_id ) {
		if ( isset( $_POST['category_color'] ) ) {
			$term_meta = get_option( 'agenda_category_meta', array() );
			$term_meta[ $term_id ] = esc_attr( $_POST['category_color'] );
			update_option( 'agenda_category_meta', $term_meta );
		}
	}

	/**
	 * Agenda post type columns.
	 */
	public function agenda_cpt_columns( $columns ) {
		$new_columns = array(
			'cb'                        => '<input type="checkbox" />',
			'title'                     => __( 'Nome do evento', 'donp-agenda' ),
			'start_date'                => __( 'Data de in&iacute;cio', 'donp-agenda' ),
			'end_date'                  => __( 'Data de t&eacute;rmino', 'donp-agenda' ),
			'taxonomy-agenda-categoria' => __( 'Categoria', 'donp-agenda' )
		);

		return $new_columns;
	}

	/**
	 * Agenda post type columns content.
	 */
	public function agenda_cpt_manage_columns( $column, $post_id ) {

		switch ( $column ) {
			case 'start_date':
				$_event_start = get_post_meta( $post_id, 'event_start', true );
				if ( ! empty( $_event_start ) ) {
					echo date( 'd/m/Y \&\a\g\r\a\v\e\;\s H:i', $_event_start );
				} else {
					echo '&mdash;';
				}
				break;
			case 'end_date':
				$_event_end = get_post_meta( $post_id, 'event_end', true );
				if ( ! empty( $_event_end ) ) {
					echo date( 'd/m/Y \&\a\g\r\a\v\e\;\s H:i', $_event_end );
				} else {
					echo '&mdash;';
				}
				break;

			default:
				break;
		}
	}

	/**
	 * Agenda taxonomy columns.
	 */
	public function agenda_taxonomy_columns( $columns ) {
		$new_columns = array(
			'cb'    => '<input type="checkbox" />',
			'name'  => __( 'Nome', 'donp-agenda' ),
			'color' => __( 'Cor', 'donp-agenda' ),
			'slug'  => __( 'Slug', 'donp-agenda' ),
			'posts' => __( 'Eventos', 'donp-agenda' )
		);

		return $new_columns;
	}

	/**
	 * Agenda taxonomy columns content.
	 */
	public function agenda_taxonomy_manage_columns( $output, $column, $term_id ) {

		switch ( $column ) {
			case 'color':
				$options = get_option( 'agenda_category_meta' );
				$color = ( is_array( $options ) && isset( $options[ $term_id ] ) ) ? $options[ $term_id ] : '#ffffff';

				$output = '<div style="width: 80px; height: 30px; border: 1px solid #999; background: ' . esc_attr( $color ) . ';"></div>';
				break;

			default:
				break;
		}

		return $output;
	}

}
