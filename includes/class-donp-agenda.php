<?php
/**
 * DONP_Agenda class.
 */
class DONP_Agenda {

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
		add_action( 'after_setup_theme', array( $this, 'post_thumbnails_support' ) );
		add_action( 'init', array( $this, 'agenda_post_type' ) );
		add_action( 'init', array( $this, 'agenda_taxonomy' ) );
		add_action( 'init', array( $this, 'rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		add_action( 'wp_head', array( $this, 'i8fix' ), 999 );
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
		if ( get_post_type() == 'agenda' && ! is_single() ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'events-calendar', plugins_url( 'assets/js/calendar.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );
			wp_enqueue_style( 'events-calendar-styles', plugins_url( 'assets/css/calendar.css', plugin_dir_path( __FILE__ ) ), array(), '' );
		}

		wp_enqueue_style( 'timeline-styles', plugins_url( 'assets/css/timeline.css', plugin_dir_path( __FILE__ ) ), array(), '' );
	}

	public function i8fix() {
		echo '<!--[if lt IE 9]>' . PHP_EOL;
		echo '<link href="' . plugins_url( 'assets/css/ie8fix.css', plugin_dir_path( __FILE__ ) ) . '" rel="stylesheet" type="text/css" />' . PHP_EOL;
		echo '<![endif]-->' . PHP_EOL;
	}

	/**
	 * Add Agenda Post Type.
	 */
	public static function agenda_post_type() {
		$labels = array(
			'name'               => __( 'Agenda', 'donp-agenda' ),
			'singular_name'      => __( 'Agenda', 'donp-agenda' ),
			'add_new'            => __( 'Adicionar novo', 'donp-agenda' ),
			'add_new_item'       => __( 'Adicionar novo Evento', 'donp-agenda' ),
			'edit_item'          => __( 'Editar Evento', 'donp-agenda' ),
			'new_item'           => __( 'Novo Evento', 'donp-agenda' ),
			'view_item'          => __( 'Ver Evento', 'donp-agenda' ),
			'search_items'       => __( 'Procurar Eventos', 'donp-agenda' ),
			'not_found'          => __( 'Nenhum evento foi encontrado', 'donp-agenda' ),
			'not_found_in_trash' => __( 'Nenhum evento na lixeira', 'donp-agenda' ),
			'parent_item_colon'  => __( 'Evento parente:', 'donp-agenda' ),
			'menu_name'          => __( 'Agenda', 'donp-agenda' ),
		);

		$args = array(
			'labels' => $labels,
			'hierarchical'        => false,
			'description'         => __( 'Agenda de eventos', 'donp-agenda' ),
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			// 'menu_position'       => 5,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'menu_icon'           => 'dashicons-calendar',
		);

		register_post_type( 'agenda', $args );
	}

	public static function agenda_taxonomy() {
		$labels = array(
			'name'              => __( 'Categorias', 'donp-agenda' ),
			'singular_name'     => __( 'Categoria', 'donp-agenda' ),
			'search_items'      => __( 'Procurar categoria', 'donp-agenda' ),
			'all_items'         => __( 'Todas as categorias', 'donp-agenda' ),
			'parent_item'       => __( 'Categoria pai', 'donp-agenda' ),
			'parent_item_colon' => __( 'Categoria pai:', 'donp-agenda' ),
			'edit_item'         => __( 'Editar categoria', 'donp-agenda' ),
			'update_item'       => __( 'Atualizar categoria', 'donp-agenda' ),
			'add_new_item'      => __( 'Adicionar nova categoria', 'donp-agenda' ),
			'new_item_name'     => __( 'Novo nome de categoria', 'donp-agenda' ),
			'menu_name'         => __( 'Categorias', 'donp-agenda' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
		);

		register_taxonomy( 'agenda-categoria', array( 'agenda' ), $args );
	}

	public static function activate() {
		self::agenda_post_type();
		self::agenda_taxonomy();

		flush_rewrite_rules();
	}

	/**
	 * Add theme support for post thumbnails.
	 */
	public function post_thumbnails_support() {
		add_theme_support( 'post-thumbnails' );
	}

	public function rewrite_rules() {
		add_rewrite_rule(
			'agenda/([0-9]{4})/([0-9]{1,2})/?$',
			'index.php?post_type_index=1&post_type=agenda&calendar_year=$matches[1]&calendar_month=$matches[2]',
			'top'
		);
	}

	public function query_vars( $query_vars ) {
		$query_vars[] = 'calendar_year';
		$query_vars[] = 'calendar_month';

		return $query_vars;
	}

	protected static function months_i18n() {
		$months = array(
			1  => __( 'Janeiro', 'donp-agenda' ),
			2  => __( 'Fevereiro', 'donp-agenda' ),
			3  => __( 'Mar&ccedil;o', 'donp-agenda' ),
			4  => __( 'Abril', 'donp-agenda' ),
			5  => __( 'Maio', 'donp-agenda' ),
			6  => __( 'Junho', 'donp-agenda' ),
			7  => __( 'Julho', 'donp-agenda' ),
			8  => __( 'Agosto', 'donp-agenda' ),
			9  => __( 'Setembro', 'donp-agenda' ),
			10 => __( 'Outubro', 'donp-agenda' ),
			11 => __( 'Novembro', 'donp-agenda' ),
			12 => __( 'Dezembro', 'donp-agenda' )
		);

		return $months;
	}

	protected static function get_years() {
		$current = date( 'Y' );

		$years = array(
			$current,
			$current + 1
		);

		return $years;
	}

	protected static function generator( $month, $year ) {

		// Start draw table.
		$calendar = '<div id="calendar">';
		$calendar .= '<table class="calendar" cellpadding="0" cellspacing="0">';

		$day_names = array(
			0 => __( 'Domingo', 'donp-agenda' ),
			1 => __( 'Segunda', 'donp-agenda' ),
			2 => __( 'Ter&ccedil;a', 'donp-agenda' ),
			3 => __( 'Quarta', 'donp-agenda' ),
			4 => __( 'Quinta', 'donp-agenda' ),
			5 => __( 'Sexta', 'donp-agenda' ),
			6 => __( 'S&aacute;bado', 'donp-agenda' )
		);

		$week_start_day = get_option( 'start_of_week' );

		// Adjust day names for sites with Monday set as the start day.
		if ( $week_start_day == 1 ) {
			$end_day = $day_names[0];
			$start_day = $day_names[1];
			array_shift( $day_names );
			$day_names[] = $end_day;
		}

		$calendar.= '<tr class="calendar-row">';
		for ( $i = 0; $i <= 6; $i++ ) {
			$calendar .= '<th class="calendar-day-head">' . $day_names[ $i ] .'</th>';
		}
		$calendar .= '</tr>';

		// Days and weeks vars now.
		$running_day = date( 'w', mktime( 0, 0, 0, $month, 1, $year ) );
		if ( $week_start_day == 1 ) {
			$running_day--;
		}
		$days_in_month = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		// Get today's date.
		$time = current_time( 'timestamp' );
		$today_day = date( 'j', $time );
		$today_month = date( 'm', $time );
		$today_year = date( 'Y', $time );

		// Row for week one.
		$calendar.= '<tr class="calendar-row">';

		// Print "blank" days until the first of the current week.
		for ( $x = 0; $x < $running_day; $x++ ) {
			$calendar .= '<td class="calendar-day-np" valign="top"></td>';
			$days_in_this_week++;
		}

		// Keep going with days.
		for ( $list_day = 1; $list_day <= $days_in_month; $list_day++ ) {

			$today = ( $today_day == $list_day && $today_month == $month && $today_year == $year ) ? 'today' : '';

			$cal_day = '<td class="calendar-day ' . $today . '" valign="top"><div class="calendar-day-wrap">';

			// Add in the day numbering.
			$cal_day .= '<div class="day-number">' . $list_day . '</div>';

			$args = array(
				'posts_per_page'      => -1,
				'post_type'           => 'agenda',
				'post_status'         => 'publish',
				'meta_key'            => 'event_start',
				'orderby'             => 'meta_value_num',
				'order'               => 'ASC',
				'meta_value'          => mktime( 0, 0, 0, $month, $list_day, $year ),
				'meta_compare'        => '>=',
				'ignore_sticky_posts' => 1
			);

			$events = get_posts( $args );

			$cal_event = '';

			$shown_events = array();

			foreach ( $events as $event ) {
				setup_postdata( $event );

				$id = $event->ID;

				$shown_events[] = $id;

				// Timestamp for start date.
				$timestamp = get_post_meta( $id, 'event_start', true );

				// Define start date.
				$evt_day    = date( 'j', $timestamp );
				$evt_month  = date( 'n', $timestamp );
				$evt_year   = date( 'Y', $timestamp );

				// Max days in the event's month.
				$last_day = date( 't', mktime( 0, 0, 0, $evt_month, 1, $evt_year ) );

				// We check if any events exists on current iteration.
				// If yes, return the link to event.
				if ( $evt_day == $list_day && $evt_month == $month && $evt_year == $year ) {
					$cal_event .= '<a class="calendar-event" href="' . get_permalink( $id ) . '"><strong>' . date( 'H:i', $timestamp ) . '</strong> ' . get_the_title( $id ) . '</a>';
				}

			}

			$calendar .= $cal_day;

			$calendar.= $cal_event ? $cal_event : '';

			$calendar.= '</div></td>';

			if ( $running_day == 6 ) {
				$calendar.= '</tr>';

				if ( ( $day_counter + 1 ) != $days_in_month ) {
					$calendar .= '<tr class="calendar-row">';
				}

				$running_day = -1;
				$days_in_this_week = 0;
			}

			$days_in_this_week++;
			$running_day++;
			$day_counter++;

		}

		// Finish the rest of the days in the week.
		if ( $days_in_this_week < 8 ) {
			for ( $x = 1; $x <= ( 8 - $days_in_this_week ); $x++ ) {
				$calendar .= '<td class="calendar-day-np" valign="top"><div class="calendar-day-wrap"></div></td>';
			}
		}

		wp_reset_postdata();

		// Final row.
		$calendar .= '</tr>';

		// End the table.
		$calendar .= '</table>';

		$calendar .= '</div>';

		// All done, return the completed table.
		return $calendar;
	}

	public static function navigation( $current_month, $current_year ) {
		$months = self::months_i18n();
		$years  = self::get_years();

		$last_month = $current_month - 1;
		$next_month = $current_month + 1;
		$last_year  = $current_year;
		$next_year  = $current_year;

		if ( $current_month == 12 ) {
			$last_month = 11;
			$next_month = 1;
			$last_year = $current_year;
			$next_year = $current_year + 1;
		}
		if ( $current_month == 1 ) {
			$last_month = 12;
			$next_month = 2;
			$last_year = $current_year - 1;
			$next_year = $current_year;
		}

		$html = '<form action="" method="post" id="calendar-form" class="form-inline">';
		$html .= '<select name="calendar_year" id="calendar-form-year">';
		foreach ( $years as $year ) {
			$form_current_year = ( $current_year == $year ) ? 'selected="selected"' : '';
			$html .= '<option ' . $form_current_year . 'value="' . $year . '">' . $year . '</option>';
		}
		$html .= '</select>';
		$html .= '<select name="calendar_month" id="calendar-form-month">';
		foreach ( $months as $key => $month ) {
			$form_current_month = ( $current_month == $key ) ? 'selected="selected"' : '';
			$html .= '<option ' . $form_current_month . 'value="' . $key . '">' . $month . '</option>';
		}
		$html .= '</select>';
		$html .= '<input type="submit" value="consultar" class="btn btn-primary" />';
		$html .= '</form>';

		$html .= '<div id="calendar-navigation" class="clearfix">';
		$html .= '<div class="row">';
		$html .= '<div class="col-md-3"><a class="btn btn-primary pull-left" href="' . home_url( 'agenda/' ) . $last_year . '/' . $last_month . '">&laquo; ' . $months[ $last_month ] . ' ' . $last_year . '</a></div>';

		$html .= '<div class="col-md-6"><h3>' . $months[ $current_month ] . ' ' . $current_year . '</h3></div>';

		$html .= '<div class="col-md-3"><a class="btn btn-primary pull-right" href="' . home_url( 'agenda/' ) . $next_year . '/' . $next_month . '">' . $months[ $next_month ] . ' ' . $next_year . ' &raquo;</a></div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public static function calendar() {
		// @date_default_timezone_set( get_option( 'timezone_string' ) );

		if ( get_query_var( 'calendar_month' ) && get_query_var( 'calendar_year' ) ) {
			$current_month = get_query_var( 'calendar_month' );
			$current_year  = get_query_var( 'calendar_year' );
		} else {
			$current_month = date( 'n' );
			$current_year  = date( 'Y' );
		}

		$html = self::navigation( $current_month, $current_year );
		$html .= self::generator( $current_month, $current_year );

		return $html;
	}

	public static function timeline() {
		wp_enqueue_script( 'mCustomScrollbar', plugins_url( 'assets/js/jquery.mCustomScrollbar.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-easing', plugins_url( 'assets/js/jquery.easing.1.3.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );
		wp_enqueue_script( 'jquery-timeline', plugins_url( 'assets/js/jquery.timeline.min.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );
		wp_enqueue_script( 'timeline-init', plugins_url( 'assets/js/timeline-init.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), '', true );

		$resizer         = DONP_Thumbnail::get_instance();
		$category_colors = get_option( 'agenda_category_meta' );
		$timeline_args   = array(
			'post_type'      => 'agenda',
			'posts_per_page' => -1,
			'meta_key'       => 'event_start',
			'meta_compare'   => 'BETWEEN',
			'meta_value'     => array( strtotime( '-5 year' ), strtotime( '+5 year' ) ),
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC'
		);
		$timeline_query = new WP_Query( $timeline_args );

		if ( $timeline_query->have_posts() ) :

			?>
			<div class="timelineLoader">
				<img src="<?php echo plugins_url( 'assets/images/timeline/loadingAnimation.gif', plugin_dir_path( __FILE__ ) ) ?>" />
			</div>

			<div id="timeline" class="timelineLight tl">
				<?php
					while ( $timeline_query->have_posts() ) :
						$timeline_query->the_post();
						$_event_start = get_post_meta( get_the_ID(), 'event_start', true );
						$event_start = date_i18n( 'd \d\e F', strtotime( date( 'Y-m-d', $_event_start ) ) );
						$category = get_the_terms( get_the_ID(), 'agenda-categoria' );
						$color = '#1a86ac';
						if ( is_array( $category ) ) {
							$term = current( $category );
							$term_id = $term->term_id;
							$color = ( is_array( $category_colors ) && isset( $category_colors[ $term_id ] ) ) ? $category_colors[ $term_id ] : '#1a86ac';
						}
				?>
					<div class="item" data-id="<?php echo date( 'd/m/Y', $_event_start ); ?>" data-description="<?php the_title(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="image_rollover_bottom" data-description="<?php _e( 'Ver detalhes', 'donp-agenda' ); ?>" href="<?php the_permalink(); ?>">
								<img src="<?php echo $resizer->process( wp_get_attachment_url( get_post_thumbnail_id() ), 230, 150, true ); ?>" alt="<?php the_title(); ?>" />
							</a>
						<?php endif; ?>
						<div style="border-top: 5px solid <?php echo esc_attr( $color ); ?>"></div>
						<h2><?php echo $event_start; ?></h2>
						<h3><?php the_title(); ?></h3>
						<span><?php echo wp_trim_words( get_the_content(), 135 ); ?></span>
						<a class="read_more" href="<?php the_permalink(); ?>"><?php _e( 'Leia mais' ); ?></a>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php wp_reset_postdata();
	}

	public function template_loader( $template ) {
		$find = array();
		$file = '';

		if ( is_post_type_archive( 'agenda' ) ) {
			$file 	= 'archive-agenda.php';
			$find[] = $file;
			$find[] = 'templates/' . $file;
		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) {
				$template = plugin_dir_path( dirname( __FILE__ ) ) . '/templates/' . $file;
			}
		}

		return $template;
	}
}
