<?php
	include('database.php');

	if (!isset($_POST['username']) || !isset($_POST['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$username = @$_POST['username'];
	$mac_address = @$_POST['mac_address'];

	$db = Database::singleton();

	// check mac address
	$check_mac_address = "select * from USERS where mac_address = '{$mac_address}';";
	$result = $db->query($check_mac_address);
	if($row = mysqli_fetch_array($result)) {
		http_response_code(422);
		exit;
	}

	// check username
	$check_username = "select * from USERS where username = '{$username}';";
	$result = $db->query($check_username);
	if($row = mysqli_fetch_array($result)) {
		http_response_code(422);
		exit;
	}

	// create a new user
	$create_user = "insert into USERS(mac_address,username) values('{$mac_address}','{$username}');";
	$db->query($create_user);

	$get_user = "select * from USERS where username = '{$username}';";
	$result = $db->query($get_user);
	if (!($row = mysqli_fetch_array($result))) {
		http_response_code(500);
		exit;
	}
	echo json_encode ($row);
	http_response_code(201);
?>
