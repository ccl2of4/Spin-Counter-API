<?php
	include('utils.php');

	if (!isset($_GET['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$mac_address = @$_GET['mac_address'];

	echo getUserByMacAddress($mac_address);
?>
