jQuery(document).ready(function($) {
	if ( $('#post-type-agenda').length === 0 ) {
		$('#calendar-form').on('submit', function(e) {
			e.preventDefault();

			var year = $('#calendar-form-year').val(),
				month = $('#calendar-form-month').val(),
				url   = $(this).attr('action');

			window.location = url + 'agenda/' + year + '/' + month + '/';
		});
	}
});
