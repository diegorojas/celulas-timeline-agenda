jQuery(document).ready(function($) {
    $('#calendar-form').on('submit', function(e) {
        e.preventDefault();

        var year = $('#calendar-form-year').val(),
            month = $('#calendar-form-month').val();

        window.location = '/agenda/' + year + '/' + month + '/';
    });
});
