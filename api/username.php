<?php
	include('database.php');

	if (!isset($_POST['username']) || !isset($_POST['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$username = @$_POST['username'];
	$mac_address = @$_POST['mac_address'];

	$db = Database::singleton();

	// check mac address to make sure there's already an account for it
	$check_mac_address = "select * from USERS where mac_address = '{$mac_address}';";
	$result = $db->query($check_mac_address);
	if(!($row = mysqli_fetch_array($result))) {
		http_response_code(422);
		exit;
	}

	// check username to make sure it's not already taken
	$check_username = "select * from USERS where username = '{$username}';";
	$result = $db->query($check_username);
	if($row = mysqli_fetch_array($result)) {
		http_response_code(422);
		exit;
	}

	// update the username
	$update = "update USERS set username = '{$username}' where mac_address = '{$mac_address}';";
	$db->query($update);

	$get_new_user = "select * from USERS where username = '{$username}';";
	$result = $db->query($get_new_user);
	if(!($row = mysqli_fetch_array($result))) {
		http_response_code(500);
		exit;
	}

	echo json_encode ($row);
	http_response_code(200);
?>
