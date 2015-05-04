<?php
	include('utils.php');

	if (!isset($_POST['username']) || !isset($_POST['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$username = @$_POST['username'];
	$mac_address = @$_POST['mac_address'];

	$user_id = createUser($mac_address, $username);


	if ($user_id == null) {
		http_response_code(409);
		exit;
	}

	echo json_encode(getUser($user_id));
	http_response_code(201);
?>
