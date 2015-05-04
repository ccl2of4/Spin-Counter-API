<?php
	require('utils.php');

	if (!isset($_GET['mac_address'])) {
		http_response_code(400);
		exit;
	}
    
	$mac_address = @$_GET['mac_address'];

	$result = getUserByMacAddress($mac_address);
	
	if ($result == null) {
		http_response_code(404);
		exit;
	}

	echo json_encode($result);
?>
