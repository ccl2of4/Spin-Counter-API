<?php
	include('utils.php');

	if (!isset($_POST['username']) || !isset($_POST['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$username = @$_POST['username'];
	$mac_address = @$_POST['mac_address'];

	$user = getUserByMacAddress($mac_address);
	if(!changeUsername($user['user_id'], $username)) {
		http_response_code(409);
		exit;
	}
?>
