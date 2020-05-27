<?php
require __DIR__ . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$input_name = test_input($_POST['inputName']);
	$input_phone = test_input($_POST['inputPhone']);
	$input_email = test_input($_POST['inputEmail']);
	$input_time = test_input($_POST['inputTime']);
	$input_date = test_input($_POST['inputDate']);
	$input_note = test_input($_POST['inputNote']);

	$end_time = strtotime($input_time);
	$end_time = date('H:i', strtotime('+1 hour', $end_time));

	$client = new Google_Client();
  $client->setApplicationName('LL TMS Calendar');
  $client->setScopes(Google_Service_Calendar::CALENDAR);
  $client->setAuthConfig('credentials.json');
  $client->setAccessType('offline');
  $client->setPrompt('select_account consent');
	
	$tokenPath = 'token.json';
	if (file_exists($tokenPath)) {
		$accessToken = json_decode(file_get_contents($tokenPath), true);
		$client->setAccessToken($accessToken);

		$service = new Google_Service_Calendar($client);

		$status_msg = array();
		try {
			$event = new Google_Service_Calendar_Event(array(
				'summary' => $input_name,
				'description' => $input_note,
				'start' => array(
					'dateTime' => $input_date.'T'.$input_time.':00+02:00',
				),
				'end' => array(
					'dateTime' => $input_date.'T'.$end_time.':00+02:00',
				),
				'reminders' => array(
					'useDefault' => FALSE,
					'overrides' => array(
						array('method' => 'popup', 'minutes' => 30),
						array('method' => 'popup', 'minutes' => 15),
					),
				),
			));

			$calendarId = 'testlltms@gmail.com';
			$event = $service->events->insert($calendarId, $event);

			$message = "Event created and can be viewed: \r\n".$event->htmlLink."\r\n";

			$status_msg = array(
				'status' => '1'
			);
			if(mail($input_email, 'Event created', $message)) {
				$status_msg['status'] = ($status_msg['status'].'1');
			}
			else {
				$status_msg['status'] = ($status_msg['status'].'0');
			}
		}
		catch (\Throwable $th) {
			$status_msg = array(
				'status' => '0',
			);
		}
	}

	print json_encode($status_msg);
	exit;
}
else {
	header('Location: index.html');
	exit;
}
  
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
?>