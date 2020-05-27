$(document).ready(function(){
	$('#inputTime').timepicker({
		timeFormat: 'HH:mm',
		interval: 15,
		minTime: '00:00',
		maxTime: '23:45',
		defaultTime: '00:00',
		startTime: '00:00',
		dynamic: false,
		dropdown: true,
		scrollbar: false
	});

	$( "#inputDate" ).datepicker({
		dateFormat: 'yy-mm-dd',
	});
});

$(document).on('submit', '.calendar-form', function(ev){
	ev.preventDefault();
 
	if(grecaptcha.getResponse()) {
		var values = $(this).serialize();

		axios(
			{
				method: 'post',
				url: 'process.php',
				data: values
			}
		)
		.then(function (response) {
			switch (response.data.status) {
				case '11':
					var alert = $('<div class="alert alert-success" role="alert">Event created and mail sent!</div>');
					$('.calendar-form').prepend(alert);

					setTimeout(() => {
						alert.remove();
					}, 5000);

					$('.calendar-form').trigger('reset');
					grecaptcha.reset();
				break;
				case '10':
					var alert = $('<div class="alert alert-warning" role="alert">Event created, but mail not sent!</div>');
					$('.calendar-form').prepend(alert);

					setTimeout(() => {
						alert.remove();
					}, 5000);

					$('.calendar-form').trigger('reset');
					grecaptcha.reset();
				break;
				case '0':
					var alert = $('<div class="alert alert-danger" role="alert">Event creation and mail sending, failed miserably!</div>');
					$('.calendar-form').prepend(alert);

					setTimeout(() => {
						alert.remove();
					}, 5000);
				break;
			}
		})
		.catch(function (error) {
			var alert = $('<div class="alert alert-danger" role="alert">An error occurred, please try again later!</div>');
			$('.calendar-form').prepend(alert);

			setTimeout(() => {
				alert.remove();
			}, 5000);
		});
	}
	else{
		if(!$('.recaptcha-error').length) {
			$('.g-recaptcha').append('<span class="recaptcha-error text-danger">Please verify that you are not a robot.</span>');
		}
	}
});
