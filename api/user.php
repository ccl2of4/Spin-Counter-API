<?php
	include('database.php');

	if (!isset($_GET['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$mac_address = @$_GET['mac_address'];

	$db = Database::singleton();


	// find user entity for mac address
	$check_mac_address = "select * from USERS where mac_address = '{$mac_address}';";
	$result = $db->query($check_mac_address);
	if($row = mysqli_fetch_array($result)) {
		echo json_encode($row);
	}
	else {
		http_response_code(404);
		exit;
	}
?>
