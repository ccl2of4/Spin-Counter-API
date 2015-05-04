<?php
	include('utils.php');

	if (!isset($_GET['user_id']) {
		http_response_code(400);
		exit;
	}
    
	$user_id = @$_GET['user_id'];

	echo getFollowedUsers($user_id);
?>
