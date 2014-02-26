<table class="form-table">
	<tr valign="top">
		<th><label for="event-start-date"><?php _e( 'Data/hora de in&iacute;cio', 'donp-agenda' ); ?></label></th>
		<td>
			<input value="<?php echo esc_attr( $current_event_start_date ); ?>" name="event_start_date" id="event-start-date" class="donp-agenda-date" type="text" class="regular-text" placeholder="dd/mm/yyyy" />
			<span> <?php _e( '&agrave;s', 'donp-agenda' ); ?> </span>
			<select id="event-start-hour" name="event_start_hour" class="donp-agenda-date-select">
				<?php
					for ( $hour = 0; $hour <= 23; $hour++ ) {
						$_hour = zeroise( $hour, 2 );
						echo sprintf( '<option %s>%s</option>', selected( $current_event_start_hour, $_hour, false ), $_hour );
					}
				?>
			</select>
			<span> : </span>
			<select id="event-start-minutes" name="event_start_minutes" class="donp-agenda-date-select">
				<?php
					for ( $minutes = 0; $minutes <= 59; $minutes++ ) {
						$_minutes = zeroise( $minutes, 2 );
						echo sprintf( '<option %s>%s</option>', selected( $current_event_start_minutes, $_minutes, false ), $_minutes );
					}
				?>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th><label for="event-end-date"><?php _e( 'Data/hora de t&eacute;rmino', 'donp-agenda' ); ?></label></th>
		<td>
			<input value="<?php echo esc_attr( $current_event_end_date ); ?>" name="event_end_date" id="event-end-date" class="donp-agenda-date" type="text" class="regular-text" placeholder="dd/mm/yyyy" />
			<span> <?php _e( '&agrave;s', 'donp-agenda' ); ?> </span>
			<select id="event-end-hour" name="event_end_hour" class="donp-agenda-date-select">
				<?php
					for ( $hour = 0; $hour <= 23; $hour++ ) {
						$_hour = zeroise( $hour, 2 );
						echo sprintf( '<option %s>%s</option>', selected( $current_event_end_hour, $_hour, false ), $_hour );
					}
				?>
			</select>
			<span> : </span>
			<select id="event-end-minutes" name="event_end_minutes" class="donp-agenda-date-select">
				<?php
					for ( $minutes = 0; $minutes <= 59; $minutes++ ) {
						$_minutes = zeroise( $minutes, 2 );
						echo sprintf( '<option %s>%s</option>', selected( $current_event_end_minutes, $_minutes, false ), $_minutes );
					}
				?>
			</select>
		</td>
	</tr>
</table>
