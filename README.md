## Template do calendario ##

Criar arquivo `archive-agenda.php` no tema.

Usar a seguinte função no lugar do loop:

	<?php donp_calendar(); ?>


## Query para a timeline ##

    $timeline_args = array(
        'post_type'      => 'agenda',
        'posts_per_page' => -1,
        'meta_key'       => 'event_start',
        'meta_compare'   => 'BETWEEN',
        'meta_value'     => array( strtotime( 'now' ), strtotime( '+3 week' ) ),
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC'
    );
    $timeline_query = new WP_Query( $timeline_args );


## Mostrar data e hora do evento ##

	// Inicio
	$_event_start = get_post_meta( $post->ID, 'event_start', true );
	if ( ! empty( $_event_start ) ) {
		echo date( 'd-m-Y H:i', $_event_start );
	}

	// Término.
	$_event_end = get_post_meta( $post->ID, 'event_end', true );
	if ( ! empty( $_event_end ) ) {
		echo date( 'd-m-Y H:i', $_event_end );
	}

## Exibir a timeline ##

	<?php donp_timeline(); ?>
