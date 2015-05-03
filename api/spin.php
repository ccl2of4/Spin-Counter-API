<?php
	include('database.php');

	if (!isset($_POST['mac_address']) || !isset($_POST['spins'])) {
		http_response_code(400);
		exit;
	}
    
	$mac_address = @$_POST['mac_address'];
	$spins = @$_POST['spins'];

	$db = Database::singleton();


	// find user entity for mac address
	$check_mac_address = "select * from USERS where mac_address = '{$mac_address}';";
	$result = $db->query($check_mac_address);
	if(!($row = mysqli_fetch_array($result))) {
		http_response_code(400);
		exit;
	}

	$max_spins = $row['max_spins'];
	$user_id = $row['user_id'];
	if ($spins > $max_spins) {
		$update = "update USERS set max_spins={$spins} where user_id={$user_id};";
		$db->query($update);
	}
?>
