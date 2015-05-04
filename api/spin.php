<?php
	include('utils.php');

	if (!isset($_POST['mac_address']) || !isset($_POST['spins'])) {
		http_response_code(400);
		exit;
	}

	$mac_address = @$_POST['mac_address'];
	$spins = @$_POST['spins'];

	$user = getUserByMacAddress($mac_address);

	reportSpins($user['user_id'], $spins);
?>
