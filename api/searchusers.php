<?php
	include('utils.php');

	if (!isset($_GET['query'])) {
		http_response_code(400);
		exit;
	}
    
	$query = @$_GET['query'];

	echo searchUsers($query);
?>
