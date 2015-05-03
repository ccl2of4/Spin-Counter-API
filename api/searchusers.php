<?php
	include('database.php');

	if (!isset($_GET['query'])) {
		http_response_code(400);
		exit;
	}
    
	$query = @$_GET['query'];

	$db = Database::singleton();

	// find user entities that match query
	$select = "select * from USERS where username like {$query}%s;";
	$result = $db->query($check_mac_address);

	$json_array = array();
	while($row = mysqli_fetch_array($result)) {
		$json_array[] = $row;
	}

	echo json_encode($json_array);
?>
